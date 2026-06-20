<?php

namespace App\Controller\Guest;

use App\Core\Application\ApplicationRepository;
use App\Database\Repository\AnnouncementsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepository,
        private readonly AnnouncementsRepository $announcementsRepository,
        private readonly RequestStack $requestStack,
    ) {}

    #[Route('/gosc', name: 'guest_dashboard')]
    public function index(): Response
    {
        return $this->render('guest/dashboard/index.html.twig', [
        ]);
    }

//    #[Route('/gosc/zmien-kontrast', name: 'guest_change_contrast')]
//    public function changeContrast(Request $request): Response
//    {
//        $session = $request->getSession();
//        $contrast = $session->get('contrast');
//
//        if (null === $contrast) {
//            $session->set('contrast', 1);
//        } else {
//            if ($contrast === 1) {
//                $session->set('contrast', 0);
//            } else {
//                $session->set('contrast', 1);
//            }
//        }
//
//        $referer = $request->headers->get('referer');
//
//        if ($referer) {
//            return new RedirectResponse($referer);
//        }
//
//        return $this->redirectToRoute('student_dashboard');
//    }
}
