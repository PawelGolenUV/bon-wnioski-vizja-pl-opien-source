<?php

namespace App\Database\Repository;

use App\Core\BasePersister;
use App\Database\Entity\OfficeRegistrationRegisteredStudent;
use App\Database\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OfficeRegistrationRegisteredStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly BasePersister $basePersister)
    {
        parent::__construct($registry, OfficeRegistrationRegisteredStudent::class);
    }

    public function update(OfficeRegistrationRegisteredStudent $registration): void
    {
        $this->basePersister->update($registration, true);
    }

    public function findByStudent(Student $student): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('rs')
            ->innerJoin('rs.registration', 'r')
            ->addSelect('r')
            ->andWhere('rs.student = :student')
            ->andWhere('rs.meetingMode IS NOT NULL')
            ->andWhere('r.startAt >= :now')
            ->setParameter('student', $student)
            ->setParameter('now', $now)
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
