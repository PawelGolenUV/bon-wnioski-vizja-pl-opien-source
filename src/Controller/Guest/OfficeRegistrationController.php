<?php

namespace App\Controller\Guest;

use App\Core\OfficeRegistrationManager\OfficeRegistrationManager;
use App\Core\Student\StudentManager;
use App\Database\Entity\OfficeRegistration;
use App\Database\Entity\OfficeRegistrationRegisteredStudent;
use App\Database\Repository\OfficeRegistrationRegisteredStudentRepository;
use App\Database\Repository\OfficeRegistrationRepository;
use App\Form\OfficeRegistration\OfficeRegistrationForm;
use App\Form\OfficeRegistration\SignUpOfficeRegistrationForm;
use App\Form\RegisterAccount\CreateAccountForm;
use App\Security\LoginFormAuthenticator;
use DateMalformedStringException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_keys;
use function array_map;
use function dump;
use function preg_match;

class OfficeRegistrationController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(private readonly StudentManager $studentManager, private readonly UserAuthenticatorInterface $userAuthenticator, private readonly LoginFormAuthenticator $authenticator,
        private readonly EntityManagerInterface $em, private readonly OfficeRegistrationManager $officeRegistrationManager, private readonly OfficeRegistrationRepository $officeRegistrationRepository, private readonly TranslatorInterface $translator, private readonly OfficeRegistrationRegisteredStudentRepository $officeRegistrationRegisteredStudentRepository,
        #[Autowire(service: 'limiter.guest_create_account')]
        private readonly RateLimiterFactory $rateLimiterFactory,
    ) {}


    #[Route('/gosc/zapisy-bon/krok-1', name: 'guest_office_registration_step_1')]
    public function createAccount(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('guest_office_registration_step_2');
        }

        $form = $this->createForm(CreateAccountForm::class);

        if ($request->isMethod('POST')) {
            $limiter = $this->rateLimiterFactory->create(
                $request->getClientIp() ?? 'unknown'
            );

            $limit = $limiter->consume(1);

            if (!$limit->isAccepted()) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('Zbyt wiele prób utworzenia konta. Spróbuj ponownie za kilka minut.')
                );

                return $this->redirectToRoute('guest_office_registration_step_1');
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();

            $user = $this->studentManager->createGuest(
                $firstName,
                $lastName,
                $email,
                $password
            );

            $this->saveTargetPath(
                $request->getSession(),
                'main',
                $this->generateUrl('guest_office_registration_step_2')
            );

            $this->addFlash(
                'success',
                $this->translator->trans('Konto zostało pomyślnie utworzone')
            );

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
        }

        return $this->render('guest/office-registration/step-1.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gosc/zapisy-bon/krok-2', name: 'guest_office_registration_step_2')]
    #[IsGranted('ROLE_GOSC')]
    public function chooseDate(Request $request): Response
    {
        $form = $this->createForm(OfficeRegistrationForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $termId = $form->get('termId')->getData();

            if (!$termId) {
                $this->addFlash('danger', $this->translator->trans('Wybierz godzinę wizyty.'));

                return $this->redirectToRoute('guest_office_registration_step_2');
            }

            $term = $this->officeRegistrationRepository->find($termId);
            if (!$term) {
                $this->addFlash('danger', $this->translator->trans('Wybrany termin nie istnieje.'));
                return $this->redirectToRoute('guest_office_registration_step_2');
            }
            $termTime = $term->startAt;
            $now = new \DateTimeImmutable();
            $canBook = true;

            if ($termTime instanceof \DateTimeImmutable) {
                $deadline = \DateTimeImmutable::createFromInterface($termTime)->modify('-12 hours');
                $canBook = $now < $deadline;
            }
            if (!$canBook) {
                $this->addFlash('danger', $this->translator->trans('Termin na rezerwację godziny już minął'));

                return $this->redirectToRoute('guest_office_registration_step_2');
            }

            $slot = $this->em->getRepository(OfficeRegistration::class)->find($termId);
            if (!$slot) {
                $this->addFlash('danger', $this->translator->trans('Wybrany termin nie istnieje.'));
                return $this->redirectToRoute('guest_office_registration_step_2');
            }

            $activeRegistration = $this->officeRegistrationRegisteredStudentRepository->findOneBy([
                'registration' => $slot,
                'confirmed' => true,
            ]);

            if ($activeRegistration) {
                $this->addFlash('danger', $this->translator->trans('Ten termin nie jest dostępny. Wybierz inny.'));
            } else {
                $registrationStudent = new OfficeRegistrationRegisteredStudent();
                $registrationStudent->registration = $slot;
                $registrationStudent->student = $this->getUser();

                $this->em->persist($registrationStudent);
                $this->em->flush();

                return $this->redirectToRoute('guest_office_registration_step_3', [
                    'id' => $slot->id,
                ]);
            }
        }

        return $this->render('guest/office-registration/step-2.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gosc/zapisy-bon/krok-3/{id}', name: 'guest_office_registration_step_3')]
    #[IsGranted('ROLE_GOSC')]
    public function confirmTerm(Request $request, OfficeRegistration $id): Response
    {
        $user = $this->getUser();

        $registrationStudent = $this->officeRegistrationRegisteredStudentRepository->findOneBy([
            'registration' => $id,
            'student' => $user,
        ]);

        if (!$registrationStudent) {
            throw $this->createAccessDeniedException('Brak dostępu do tej wizyty');
        }

        $students = $this->officeRegistrationRegisteredStudentRepository->findBy(['registration' => $id]);

        foreach ($students as $student) {
            $isCompleted = $student->meetingMode !== null && $student->description !== null;

            $isAnotherStudent = $student !== $user;

            $blockTerm = $student->confirmed !== false;

            if ($isCompleted && $isAnotherStudent && $blockTerm) {
                $this->addFlash('error', $this->translator->trans('Termin jest już zajęty'));

                return $this->redirectToRoute('guest_office_registration_step_2');
            }
        }

        $termTime = $id->startAt;
        $now = new \DateTimeImmutable();
        $canBook = true;

        if ($termTime instanceof \DateTimeImmutable) {
            $deadline = \DateTimeImmutable::createFromInterface($termTime)->modify('-12 hours');
            $canBook = $now < $deadline;
        }
        if (!$canBook) {
            $this->addFlash('danger', $this->translator->trans('Termin na rezerwację godziny już minął'));

            return $this->redirectToRoute('guest_office_registration_step_2');
        }

        $form = $this->createForm(SignUpOfficeRegistrationForm::class, $registrationStudent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->officeRegistrationRegisteredStudentRepository->update($data);

            return $this->redirectToRoute('guest_office_registration_step_4',
                [
                    'id' => $id->id,
                ]);
        }

        return $this->render('guest/office-registration/step-3.html.twig', [
            'registration' => $registrationStudent,
            'form' => $form->createView(),
            'officeRegistration' => $id,
        ]);
    }

    #[Route('/gosc/zapisy-bon/krok-4/{id}', name: 'guest_office_registration_step_4')]
    #[IsGranted('ROLE_GOSC')]
    public function summaryTerm(Request $request, OfficeRegistration $id): Response
    {
        $user = $this->getUser();
        $myRegistration = null;

        $this->addFlash('success', $this->translator->trans('Prośba o rezerwację wizyty została wysłana i oczekuje na potwierdzenie przez pracownika BON'));

        foreach ($id->registeredStudents as $rs) {
            if ($rs->student === $user) {
                $myRegistration = $rs;
                break;
            }
        }

        if (!$myRegistration) {
            throw $this->createAccessDeniedException('Brak dostępu do tej wizyty');
        }

        return $this->render('guest/office-registration/step-4.html.twig', [
            'registration' => $id,
            'myRegistration' => $myRegistration,
        ]);
    }

    #[Route('/gosc/zapisy-bon/anuluj-rezerwacje/{id}', name: 'guest_office_registration_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_GOSC')]
    public function cancelTerm(OfficeRegistrationRegisteredStudent $id, Request $request): Response
    {
        $user = $this->getUser();

        $registrationStudent = $this->officeRegistrationRegisteredStudentRepository->findOneBy([
            'registration' => $id->registration->id,
            'student' => $user,
        ]);

        if (!$this->isCsrfTokenValid('cancel_registration_' . $id->id, $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                $this->translator->trans('Nie udało się zweryfikować bezpieczeństwa żądania. Odśwież stronę i spróbuj ponownie')
            );
        }

        if (!$registrationStudent) {
            throw $this->createAccessDeniedException('Brak dostępu do tej wizyty');
        }

        $now = new \DateTimeImmutable();
        $term = $id->registration->startAt;

        $canCancel = false;

        if ($term instanceof \DateTimeInterface) {
            $deadline = \DateTimeImmutable::createFromInterface($term)->modify('-24 hours');
            $canCancel = $now < $deadline;
        }

        if (!$canCancel) {
            $this->addFlash('danger', $this->translator->trans('Termin na anulowanie wizyty już minął'));

            return $this->redirectToRoute('guest_registration_center_my_registrations');
        }

        $registrationStudent->confirmed = false;

        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Rezerwacja anulowana'));

        return $this->redirectToRoute('guest_registration_center_my_registrations');
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Route('/gosc/api/office-terms', name: 'guest_office_terms', methods: ['GET'])]
    #[IsGranted('ROLE_GOSC')]
    public function guestOfficeTerms(Request $request): JsonResponse
    {
        $date = (string)$request->query->get('date'); // YYYY-MM-DD

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->json(['slots' => []]);
        }

        try {
            $start = new \DateTimeImmutable($date . ' 00:00:00');
        } catch (\Throwable) {
            return $this->json(['slots' => []]);
        }

        $end = $start->modify('+1 day');
//        $now = new \DateTimeImmutable();
        $now = (new \DateTimeImmutable())->modify('+12 hours');

        $qb = $this->em->getRepository(OfficeRegistration::class)
            ->createQueryBuilder('s');

        $slots = $qb
            ->leftJoin(
                's.registeredStudents',
                'rs',
                'WITH',
                '(rs.confirmed = true) OR (rs.confirmed IS NULL AND rs.meetingMode IS NOT NULL)'
            )
            ->andWhere('rs.id IS NULL')
            ->andWhere('s.startAt >= :start')
            ->andWhere('s.startAt < :end')
            ->andWhere('s.startAt >= :now')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('now', $now)
            ->orderBy('s.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        $out = array_map(static function (OfficeRegistration $s) {
            return [
                'id' => $s->id,
                'time' => $s->startAt->format('H:i') . ' - ' . $s->endAt->format('H:i'),
            ];
        }, $slots);

        return $this->json(['slots' => $out]);
    }

    #[Route('/gosc/api/office-terms/days', name: 'guest_office_terms_days', methods: ['GET'])]
    #[IsGranted('ROLE_GOSC')]
    public function officeTermsDays(Request $request): JsonResponse
    {
        $month = (string)$request->query->get('month'); // YYYY-MM

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $this->json(['days' => []]);
        }

        try {
            $start = new \DateTimeImmutable($month . '-01 00:00:00');
        } catch (\Throwable) {
            return $this->json(['days' => []]);
        }

        $end = $start->modify('first day of next month');
//        $now = new \DateTimeImmutable();
        $now = (new \DateTimeImmutable())->modify('+12 hours');

        $qb = $this->em->getRepository(OfficeRegistration::class)
            ->createQueryBuilder('s');

        $rows = $qb
            ->select('s.startAt')
            ->leftJoin(
                's.registeredStudents',
                'rs',
                'WITH',
                '(rs.confirmed = true) OR (rs.confirmed IS NULL AND rs.meetingMode IS NOT NULL)'
            )
            ->andWhere('rs.id IS NULL')
            ->andWhere('rs.meetingMode IS NULL')
            ->andWhere('s.startAt >= :start')
            ->andWhere('s.startAt < :end')
            ->andWhere('s.startAt >= :now')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('now', $now)
            ->orderBy('s.startAt', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $set = [];
        foreach ($rows as $row) {
            if (!isset($row['startAt']) || !$row['startAt'] instanceof \DateTimeInterface) {
                continue;
            }

            $set[$row['startAt']->format('Y-m-d')] = true;
        }

        return $this->json(['days' => array_keys($set)]);
    }
}
