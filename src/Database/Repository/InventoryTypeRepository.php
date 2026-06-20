<?php

namespace App\Database\Repository;

use App\Database\Entity\Dictionary\Item;
use App\Database\Entity\InventoryType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class InventoryTypeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $managerRegistry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, InventoryType::class);
    }

    /**
     * @param Item $item
     * @return array
     */
    public function findByDictionaryItemType(Item $item): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.inv', 'eq')->addSelect('eq') // dociągamy Item, by nie było N+1
            ->andWhere('i.inv = :item')
            ->setParameter('item', $item)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function countByType(): array
    {
        return $this->createQueryBuilder('i')              // i = Inventory
        ->select('t.id AS typeId, COUNT(i.id) AS cnt')
            ->join('i.inv', 't')                // <-- nazwa pola asocjacji!
            ->groupBy('t.id')
            ->getQuery()->getArrayResult();
    }

    /**
     * @param string $inventoryTypeId
     * @return InventoryType|null
     */
    public function findOneById(string $inventoryTypeId): ?InventoryType
    {
        return $this->find($inventoryTypeId);
    }

}
