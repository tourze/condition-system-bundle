<?php

namespace Tourze\ConditionSystemBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;
use Tourze\ConditionSystemBundle\Entity\TestCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * TestCondition 实体测试
 * @internal
 */
#[CoversClass(TestCondition::class)]
final class TestConditionTest extends AbstractEntityTestCase
{
    protected function createEntity(): TestCondition
    {
        return new TestCondition();
    }

    public function testGetTrigger(): void
    {
        $condition = $this->createEntity();
        $reflection = new \ReflectionClass($condition);
        $property = $reflection->getProperty('triggerType');
        $property->setAccessible(true);
        $property->setValue($condition, ConditionTrigger::AFTER_ACTION->value);

        $this->assertEquals(ConditionTrigger::AFTER_ACTION, $condition->getTrigger());
    }

    public function testSetTriggerType(): void
    {
        $condition = $this->createEntity();
        $condition->setTriggerType(ConditionTrigger::VALIDATION);

        $this->assertEquals(ConditionTrigger::VALIDATION, $condition->getTrigger());
    }

    public function testToArray(): void
    {
        $condition = $this->createEntity();
        $condition->setType('test_type');
        $condition->setLabel('Test Label');
        $condition->setEnabled(true);
        $condition->setTriggerType(ConditionTrigger::BEFORE_ACTION);

        $result = $condition->toArray();

        $expected = [
            'id' => null,
            'type' => 'test_type',
            'label' => 'Test Label',
            'trigger' => ConditionTrigger::BEFORE_ACTION->value,
            'enabled' => true,
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetSubject(): void
    {
        $condition = $this->createEntity();
        $this->assertNull($condition->getSubject());
    }

    public function testInheritance(): void
    {
        $condition = $this->createEntity();
        $this->assertInstanceOf(BaseCondition::class, $condition);
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'type' => ['type', 'test_condition'];
        yield 'label' => ['label', 'Test Condition'];
        yield 'remark' => ['remark', 'Test Remark'];
        yield 'enabled' => ['enabled', true];
        yield 'createTime' => ['createTime', new \DateTimeImmutable('2023-01-01 10:00:00')];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable('2023-01-02 15:30:00')];
    }
}
