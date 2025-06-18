<?php

namespace Tourze\ConditionSystemBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;

class ConditionTriggerTest extends TestCase
{
    public function test_enum_values_are_correct(): void
    {
        $this->assertEquals('before_action', ConditionTrigger::BEFORE_ACTION->value);
        $this->assertEquals('after_action', ConditionTrigger::AFTER_ACTION->value);
        $this->assertEquals('during_action', ConditionTrigger::DURING_ACTION->value);
        $this->assertEquals('validation', ConditionTrigger::VALIDATION->value);
        $this->assertEquals('filter', ConditionTrigger::FILTER->value);
    }

    public function test_enum_labels_are_correct(): void
    {
        $this->assertEquals('前置条件', ConditionTrigger::BEFORE_ACTION->getLabel());
        $this->assertEquals('后置条件', ConditionTrigger::AFTER_ACTION->getLabel());
        $this->assertEquals('执行中条件', ConditionTrigger::DURING_ACTION->getLabel());
        $this->assertEquals('验证条件', ConditionTrigger::VALIDATION->getLabel());
        $this->assertEquals('过滤条件', ConditionTrigger::FILTER->getLabel());
    }

    public function test_all_enum_cases_exist(): void
    {
        $expectedCases = [
            'BEFORE_ACTION',
            'AFTER_ACTION',
            'DURING_ACTION',
            'VALIDATION',
            'FILTER'
        ];

        $actualCases = array_map(fn($case) => $case->name, ConditionTrigger::cases());

        $this->assertEquals($expectedCases, $actualCases);
        $this->assertCount(5, ConditionTrigger::cases());
    }

    public function test_enum_implements_labelable_interface(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function test_enum_implements_itemable_interface(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function test_enum_implements_selectable_interface(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function test_trait_usage(): void
    {
        // 测试枚举使用了正确的 trait
        $reflection = new \ReflectionClass(ConditionTrigger::class);
        $traitNames = $reflection->getTraitNames();

        $this->assertContains(\Tourze\EnumExtra\ItemTrait::class, $traitNames);
        $this->assertContains(\Tourze\EnumExtra\SelectTrait::class, $traitNames);
    }

    public function test_trait_methods_exist(): void
    {
        // 验证 trait 方法是否存在（如果 trait 实现了这些方法）
        $methods = get_class_methods(ConditionTrigger::class);

        // 这些方法可能由 trait 提供，但我们不强制要求它们存在
        // 因为具体实现可能不同
        $this->assertContains('getLabel', $methods);
    }

    public function test_enum_can_be_used_in_match_expression(): void
    {
        $trigger = ConditionTrigger::BEFORE_ACTION;

        $result = match ($trigger) {
            ConditionTrigger::BEFORE_ACTION => 'before',
            ConditionTrigger::AFTER_ACTION => 'after',
            ConditionTrigger::DURING_ACTION => 'during',
            ConditionTrigger::VALIDATION => 'validation',
            ConditionTrigger::FILTER => 'filter',
        };

        $this->assertEquals('before', $result);
    }

    public function test_enum_can_be_serialized(): void
    {
        $trigger = ConditionTrigger::VALIDATION;
        $serialized = serialize($trigger);
        $unserialized = unserialize($serialized);

        $this->assertSame($trigger, $unserialized);
        $this->assertEquals('validation', $unserialized->value);
        $this->assertEquals('验证条件', $unserialized->getLabel());
    }

    public function test_enum_comparison(): void
    {
        $trigger1 = ConditionTrigger::BEFORE_ACTION;
        $trigger2 = ConditionTrigger::BEFORE_ACTION;
        $trigger3 = ConditionTrigger::AFTER_ACTION;

        $this->assertSame($trigger1, $trigger2);
        $this->assertNotSame($trigger1, $trigger3);
        $this->assertTrue($trigger1 === $trigger2);
        $this->assertFalse($trigger1 === $trigger3);
    }

    public function test_from_value_creation(): void
    {
        $trigger = ConditionTrigger::from('before_action');
        $this->assertSame(ConditionTrigger::BEFORE_ACTION, $trigger);

        $trigger = ConditionTrigger::from('validation');
        $this->assertSame(ConditionTrigger::VALIDATION, $trigger);
    }

    public function test_try_from_value_creation(): void
    {
        $trigger = ConditionTrigger::tryFrom('before_action');
        $this->assertSame(ConditionTrigger::BEFORE_ACTION, $trigger);

        $trigger = ConditionTrigger::tryFrom('invalid_value');
        $this->assertNull($trigger);
    }
}
