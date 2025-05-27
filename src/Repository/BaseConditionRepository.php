<?php

namespace Tourze\ConditionSystemBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;

/**
 * 基础条件仓储类
 */
class BaseConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseCondition::class);
    }

    /**
     * 根据类型查找条件
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找启用的条件
     */
    public function findEnabled(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据类型查找启用的条件
     */
    public function findEnabledByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->andWhere('c.enabled = :enabled')
            ->setParameter('type', $type)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }
} 