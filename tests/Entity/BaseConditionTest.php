<?php

namespace Tourze\ConditionSystemBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(BaseCondition::class)]
final class BaseConditionTest extends AbstractEntityTestCase
{
    protected function createEntity(): BaseCondition
    {
        // 创建抽象类的匿名实现用于测试
        $condition = new class extends BaseCondition {
            public function getTrigger(): ConditionTrigger
            {
                return ConditionTrigger::BEFORE_ACTION;
            }

            public function toArray(): array
            {
                return [
                    'id' => $this->getId(),
                    'type' => $this->getType(),
                    'label' => $this->getLabel(),
                    'enabled' => $this->isEnabled(),
                ];
            }

            public function getSubject(): ?SubjectInterface
            {
                return null;
            }
        };

        // 初始化必需的属性
        $condition->setType('test_type');
        $condition->setLabel('测试条件');

        return $condition;
    }

    public function testAbstractMethodsAreImplemented(): void
    {
        $condition = $this->createEntity();
        // 测试抽象方法的实现
        $this->assertEquals(ConditionTrigger::BEFORE_ACTION, $condition->getTrigger());
        $this->assertCount(4, $condition->toArray());
        $this->assertNull($condition->getSubject());
    }

    public function testToArrayReturnsExpectedStructure(): void
    {
        $condition = $this->createEntity();
        $condition->setType('test_type');
        $condition->setLabel('测试条件');
        $condition->setEnabled(false);

        $array = $condition->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('enabled', $array);

        $this->assertEquals('test_type', $array['type']);
        $this->assertEquals('测试条件', $array['label']);
        $this->assertFalse($array['enabled']);
    }

    public function testImplementsConditionInterface(): void
    {
        $condition = $this->createEntity();
        $this->assertInstanceOf(
            ConditionInterface::class,
            $condition
        );
    }

    public function testBasicProperties(): void
    {
        $condition = $this->createEntity();
        $condition->setType('chained_type');
        $condition->setLabel('链式调用测试');
        $condition->setRemark('测试链式调用');
        $condition->setEnabled(false);

        $this->assertEquals('chained_type', $condition->getType());
        $this->assertEquals('链式调用测试', $condition->getLabel());
        $this->assertEquals('测试链式调用', $condition->getRemark());
        $this->assertFalse($condition->isEnabled());
    }

    public function testDatetimeImmutableSupport(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 15:30:00');

        $condition = $this->createEntity();
        $condition->setCreateTime($createTime);
        $condition->setUpdateTime($updateTime);

        $this->assertSame($createTime, $condition->getCreateTime());
        $this->assertSame($updateTime, $condition->getUpdateTime());
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
