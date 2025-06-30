<?php

namespace Tourze\ConditionSystemBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\ConditionSystemBundle\Repository\BaseConditionRepository;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;

class BaseConditionRepositoryTest extends TestCase
{
    private BaseConditionRepository $repository;
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        
        $metadata = new ClassMetadata(BaseCondition::class);
        
        $this->entityManager->method('getClassMetadata')
            ->willReturn($metadata);
        
        $this->registry->method('getManagerForClass')
            ->with(BaseCondition::class)
            ->willReturn($this->entityManager);
        
        $this->repository = new BaseConditionRepository($this->registry);
    }

    public function test_repository_instance_creation(): void
    {
        $this->assertInstanceOf(BaseConditionRepository::class, $this->repository);
    }

    public function test_find_enabled_conditions(): void
    {
        // 测试方法的返回类型注解
        $reflection = new \ReflectionMethod($this->repository, 'findEnabled');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('array', (string) $returnType);
    }

    public function test_find_by_type(): void
    {
        // 测试方法的返回类型注解
        $reflection = new \ReflectionMethod($this->repository, 'findByType');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('array', (string) $returnType);
        
        // 测试方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('type', $parameters[0]->getName());
        $paramType = $parameters[0]->getType();
        $this->assertNotNull($paramType);
        $this->assertEquals('string', (string) $paramType);
    }
}