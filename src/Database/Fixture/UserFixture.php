<?php

declare(strict_types=1);

namespace App\Database\Fixture;

use App\Database\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixture
 * @package App\Database\Fixture
 */
class UserFixture extends Fixture implements OrderedFixtureInterface
{

    /**
     * UserFixture constructor
     */
    public function __construct()
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'email' => 'p.golen@vizja.pl',
                'first_name' => 'Paweł',
                'is_active' => true,
                'last_name' => 'Goleń',
                'roles' => [
                    'ROLE_ADMIN',
                ],
            ],
            [
                'email' => 'm.zielinski@vizja.pl',
                'first_name' => 'Marcin',
                'is_active' => true,
                'last_name' => 'Zieliński',
                'roles' => [
                    'ROLE_ADMIN',
                ],
            ]
        ];

        foreach ($data as $user) {
            $userEntity = new User();
            $userEntity->email = $user['email'];
            $userEntity->firstName = $user['first_name'];
            $userEntity->isActive = $user['is_active'];
            $userEntity->lastName = $user['last_name'];
            $userEntity->roles = $user['roles'];

            $manager->persist($userEntity);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 100;
    }
}
