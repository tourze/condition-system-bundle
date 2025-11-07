<?php

namespace Tourze\ConditionSystemBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 基础条件仓储类
 *
 * @extends ServiceEntityRepository<BaseCondition>
 */
#[Autoconfigure(public: true)]
#[AsRepository(entityClass: BaseCondition::class)]
class BaseConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseCondition::class);
    }

    /**
     * 根据类型查找条件
     *
     * @return array<BaseCondition>
     */
    public function findByType(string $type): array
    {
        /** @var array<BaseCondition> */
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 查找启用的条件
     *
     * @return array<BaseCondition>
     */
    public function findEnabled(): array
    {
        /** @var array<BaseCondition> */
        return $this->createQueryBuilder('c')
            ->andWhere('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 根据类型查找启用的条件
     *
     * @return array<BaseCondition>
     */
    public function findEnabledByType(string $type): array
    {
        /** @var array<BaseCondition> */
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->andWhere('c.enabled = :enabled')
            ->setParameter('type', $type)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 保存实体
     */
    public function save(BaseCondition $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除实体
     */
    public function remove(BaseCondition $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
