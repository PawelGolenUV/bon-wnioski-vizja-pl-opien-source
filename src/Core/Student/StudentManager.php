<?php

declare(strict_types=1);

namespace App\Core\Student;

use App\Core\BaseManager;
use App\Database\Entity\Application;
use App\Database\Entity\Student;
use App\Verbis\API\PobierzOsobe;
use DateMalformedStringException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function dump;

/**
 * Class UserManager
 * @package App\Core\User
 */
final class StudentManager extends BaseManager
{
    /**
     * StudentManager constructor
     * @param StudentRepository $studentRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly StudentRepository $studentRepository, private readonly EntityManagerInterface $entityManager, private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {}

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->studentRepository->getAll();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this->studentRepository->getAllQueryBuilder();
    }

    /**
     * @param string $email
     * @return Student
     */
    public function getOneByEmail(string $email): Student
    {
        $user = $this->studentRepository->getOneByEmail($email);

        if (!$user instanceof Student) {
            throw new NotFoundHttpException('Nie znaleziono użytkownika');
        }

        return $user;
    }

    /**
     * @param string $userId
     * @return Student
     */
    public function getOneById(string $userId): Student
    {
        $student = $this->studentRepository->getOneById($userId);

        if (!$student instanceof Student) {
            throw new NotFoundHttpException('Nie znaleziono użytkownika');
        }

        return $student;
    }

    /**
     * @param Student $student
     * @param bool $flush
     * @return void
     */
    public function delete(Student $student, bool $flush = true): void
    {
        $this->entityManager->remove($student);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Student $student
     * @return void
     */
    public function update(Student $student): void
    {
        $this->basePersister->update($student, true);
    }

    /**
     * @param Student $student
     * @param bool $flush
     * @return void
     */
    public function block(Student $student, bool $flush = true): void
    {
        $student->isActive = false;

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Student $student
     * @param bool $flush
     * @return void
     */
    public function restore(Student $student, bool $flush = true): void
    {
        $student->isActive = true;

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Student $student
     * @param Application $application
     * @return void
     */
    public function updateStudentInfo(Student $student, Application $application): void
    {
        if ($student->albumNumber === null) {
            $student->albumNumber = $application->albumNumber;
        }

        if ($student->phone === null) {
            $student->phone = $application->phone;
        }

        if ($student->faculty === null) {
            $student->faculty = $application->faculty;
        }

        if ($student->studyMode === null) {
            $student->studyMode = $application->studyMode;
        }

        if ($student->studyYear === null) {
            $student->studyYear = $application->year;
        }

        if ($student->studySemester === null) {
            $student->studySemester = $application->semester;
        }
    }

    public function createGuest(string $firstName, string $lastName, string $email, string $password): Student
    {
        $repo = $this->entityManager->getRepository(Student::class);
        $existing = $repo->findOneBy(['email' => $email]);

        if ($existing instanceof Student) {
            throw new RuntimeException('Użytkownik o tym adresie email już istnieje.');
        }

        $guest = new Student();
        $guest->firstName = $firstName;
        $guest->lastName = $lastName;
        $guest->email = $email;
        $guest->password = $this->userPasswordHasher->hashPassword($guest, $password);
        $guest->isActive = true;
        $guest->roles = ['ROLE_GOSC'];

        $this->entityManager->persist($guest);
        $this->entityManager->flush();

        return $guest;
    }

}
