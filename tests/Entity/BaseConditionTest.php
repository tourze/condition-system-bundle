<?php

namespace Tourze\ConditionSystemBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;

class BaseConditionTest extends TestCase
{
    private BaseCondition $condition;

    protected function setUp(): void
    {
        // 创建抽象类的匿名实现用于测试
        $this->condition = new class extends BaseCondition {
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
        $this->condition->setType('test_type');
        $this->condition->setLabel('测试条件');
    }

    public function test_id_is_initially_null(): void
    {
        $this->assertNull($this->condition->getId());
    }

    public function test_type_can_be_set_and_retrieved(): void
    {
        $type = 'user_permission';
        $result = $this->condition->setType($type);
        
        $this->assertSame($this->condition, $result); // 测试流式接口
        $this->assertEquals($type, $this->condition->getType());
    }

    public function test_label_can_be_set_and_retrieved(): void
    {
        $label = '用户权限检查';
        $result = $this->condition->setLabel($label);
        
        $this->assertSame($this->condition, $result);
        $this->assertEquals($label, $this->condition->getLabel());
    }

    public function test_remark_can_be_set_and_retrieved(): void
    {
        $remark = '检查用户是否有访问权限';
        $result = $this->condition->setRemark($remark);
        
        $this->assertSame($this->condition, $result);
        $this->assertEquals($remark, $this->condition->getRemark());
    }

    public function test_remark_can_be_null(): void
    {
        $this->condition->setRemark('some remark');
        $result = $this->condition->setRemark(null);
        
        $this->assertSame($this->condition, $result);
        $this->assertNull($this->condition->getRemark());
    }

    public function test_enabled_is_true_by_default(): void
    {
        $this->assertTrue($this->condition->isEnabled());
    }

    public function test_enabled_can_be_set_and_retrieved(): void
    {
        $result = $this->condition->setEnabled(false);
        
        $this->assertSame($this->condition, $result);
        $this->assertFalse($this->condition->isEnabled());
        
        $this->condition->setEnabled(true);
        $this->assertTrue($this->condition->isEnabled());
    }

    public function test_create_time_can_be_set_and_retrieved(): void
    {
        $createTime = new \DateTime('2023-01-01 10:00:00');
        $result = $this->condition->setCreateTime($createTime);
        
        $this->assertSame($this->condition, $result);
        $this->assertSame($createTime, $this->condition->getCreateTime());
    }

    public function test_create_time_can_be_null(): void
    {
        $this->condition->setCreateTime(new \DateTime());
        $result = $this->condition->setCreateTime(null);
        
        $this->assertSame($this->condition, $result);
        $this->assertNull($this->condition->getCreateTime());
    }

    public function test_update_time_can_be_set_and_retrieved(): void
    {
        $updateTime = new \DateTime('2023-01-02 15:30:00');
        $result = $this->condition->setUpdateTime($updateTime);
        
        $this->assertSame($this->condition, $result);
        $this->assertSame($updateTime, $this->condition->getUpdateTime());
    }

    public function test_update_time_can_be_null(): void
    {
        $this->condition->setUpdateTime(new \DateTime());
        $result = $this->condition->setUpdateTime(null);
        
        $this->assertSame($this->condition, $result);
        $this->assertNull($this->condition->getUpdateTime());
    }

    public function test_abstract_methods_are_implemented(): void
    {
        // 测试抽象方法的实现
        $this->assertEquals(ConditionTrigger::BEFORE_ACTION, $this->condition->getTrigger());
        $this->assertIsArray($this->condition->toArray());
        $this->assertNull($this->condition->getSubject());
    }

    public function test_to_array_returns_expected_structure(): void
    {
        $this->condition
            ->setType('test_type')
            ->setLabel('测试条件')
            ->setEnabled(false);
        
        $array = $this->condition->toArray();
        
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('enabled', $array);
        
        $this->assertEquals('test_type', $array['type']);
        $this->assertEquals('测试条件', $array['label']);
        $this->assertFalse($array['enabled']);
    }

    public function test_implements_condition_interface(): void
    {
        $this->assertInstanceOf(
            \Tourze\ConditionSystemBundle\Interface\ConditionInterface::class,
            $this->condition
        );
    }

    public function test_fluent_interface_chaining(): void
    {
        $createTime = new \DateTime();
        $updateTime = new \DateTime();
        
        $result = $this->condition
            ->setType('chained_type')
            ->setLabel('链式调用测试')
            ->setRemark('测试链式调用')
            ->setEnabled(false)
            ->setCreateTime($createTime)
            ->setUpdateTime($updateTime);
        
        $this->assertSame($this->condition, $result);
        $this->assertEquals('chained_type', $this->condition->getType());
        $this->assertEquals('链式调用测试', $this->condition->getLabel());
        $this->assertEquals('测试链式调用', $this->condition->getRemark());
        $this->assertFalse($this->condition->isEnabled());
        $this->assertSame($createTime, $this->condition->getCreateTime());
        $this->assertSame($updateTime, $this->condition->getUpdateTime());
    }

    public function test_datetime_immutable_support(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 15:30:00');
        
        $this->condition->setCreateTime($createTime);
        $this->condition->setUpdateTime($updateTime);
        
        $this->assertSame($createTime, $this->condition->getCreateTime());
        $this->assertSame($updateTime, $this->condition->getUpdateTime());
    }
} 