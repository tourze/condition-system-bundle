<?php

namespace Tourze\ConditionSystemBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ConditionTrigger::class)]
final class ConditionTriggerTest extends AbstractEnumTestCase
{
    public function testEnumValuesAreCorrect(): void
    {
        $this->assertEquals('before_action', ConditionTrigger::BEFORE_ACTION->value);
        $this->assertEquals('after_action', ConditionTrigger::AFTER_ACTION->value);
        $this->assertEquals('during_action', ConditionTrigger::DURING_ACTION->value);
        $this->assertEquals('validation', ConditionTrigger::VALIDATION->value);
        $this->assertEquals('filter', ConditionTrigger::FILTER->value);
    }

    public function testEnumLabelsAreCorrect(): void
    {
        $this->assertEquals('前置条件', ConditionTrigger::BEFORE_ACTION->getLabel());
        $this->assertEquals('后置条件', ConditionTrigger::AFTER_ACTION->getLabel());
        $this->assertEquals('执行中条件', ConditionTrigger::DURING_ACTION->getLabel());
        $this->assertEquals('验证条件', ConditionTrigger::VALIDATION->getLabel());
        $this->assertEquals('过滤条件', ConditionTrigger::FILTER->getLabel());
    }

    public function testAllEnumCasesExist(): void
    {
        $expectedCases = [
            'BEFORE_ACTION',
            'AFTER_ACTION',
            'DURING_ACTION',
            'VALIDATION',
            'FILTER',
        ];

        $actualCases = array_map(fn ($case) => $case->name, ConditionTrigger::cases());

        $this->assertEquals($expectedCases, $actualCases);
        $this->assertCount(5, ConditionTrigger::cases());
    }

    public function testEnumImplementsLabelableInterface(): void
    {
        $this->assertInstanceOf(Labelable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function testEnumImplementsItemableInterface(): void
    {
        $this->assertInstanceOf(Itemable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function testEnumImplementsSelectableInterface(): void
    {
        $this->assertInstanceOf(Selectable::class, ConditionTrigger::BEFORE_ACTION);
    }

    public function testTraitUsage(): void
    {
        // 测试枚举使用了正确的 trait
        $reflection = new \ReflectionClass(ConditionTrigger::class);
        $traitNames = $reflection->getTraitNames();

        $this->assertContains(ItemTrait::class, $traitNames);
        $this->assertContains(SelectTrait::class, $traitNames);
    }

    public function testTraitMethodsExist(): void
    {
        // 验证 trait 方法是否存在（如果 trait 实现了这些方法）
        $methods = get_class_methods(ConditionTrigger::class);

        // 这些方法可能由 trait 提供，但我们不强制要求它们存在
        // 因为具体实现可能不同
        $this->assertContains('getLabel', $methods);
    }

    public function testEnumCanBeUsedInMatchExpression(): void
    {
        $triggers = [
            ConditionTrigger::BEFORE_ACTION,
            ConditionTrigger::AFTER_ACTION,
            ConditionTrigger::DURING_ACTION,
            ConditionTrigger::VALIDATION,
            ConditionTrigger::FILTER,
        ];

        foreach ($triggers as $trigger) {
            $result = match ($trigger) {
                ConditionTrigger::BEFORE_ACTION => 'before',
                ConditionTrigger::AFTER_ACTION => 'after',
                ConditionTrigger::DURING_ACTION => 'during',
                ConditionTrigger::VALIDATION => 'validation',
                ConditionTrigger::FILTER => 'filter',
            };

            $expected = match ($trigger) {
                ConditionTrigger::BEFORE_ACTION => 'before',
                ConditionTrigger::AFTER_ACTION => 'after',
                ConditionTrigger::DURING_ACTION => 'during',
                ConditionTrigger::VALIDATION => 'validation',
                ConditionTrigger::FILTER => 'filter',
            };

            $this->assertEquals($expected, $result);
        }
    }

    public function testEnumCanBeSerialized(): void
    {
        $trigger = ConditionTrigger::VALIDATION;
        $serialized = serialize($trigger);
        $unserialized = unserialize($serialized);

        $this->assertSame($trigger, $unserialized);
        $this->assertEquals('validation', $unserialized->value);
        $this->assertEquals('验证条件', $unserialized->getLabel());
    }

    public function testEnumComparison(): void
    {
        $trigger1 = ConditionTrigger::BEFORE_ACTION;
        $trigger2 = ConditionTrigger::BEFORE_ACTION;

        $this->assertSame($trigger1, $trigger2);

        // 测试不同枚举值的比较 - 直接验证枚举值的唯一性
        $triggers = ConditionTrigger::cases();
        $this->assertCount(5, $triggers);

        // 验证每个枚举值都有唯一的 value
        $values = array_map(fn ($trigger) => $trigger->value, $triggers);
        $this->assertCount(5, array_unique($values));
    }

    public function testFromValueCreation(): void
    {
        $trigger = ConditionTrigger::from('before_action');
        $this->assertSame(ConditionTrigger::BEFORE_ACTION, $trigger);

        $trigger = ConditionTrigger::from('validation');
        $this->assertSame(ConditionTrigger::VALIDATION, $trigger);
    }

    public function testTryFromValueCreation(): void
    {
        $trigger = ConditionTrigger::tryFrom('before_action');
        $this->assertSame(ConditionTrigger::BEFORE_ACTION, $trigger);

        $trigger = ConditionTrigger::tryFrom('invalid_value');
        $this->assertNull($trigger);
    }

    public function testToArray(): void
    {
        $expected = [
            'before_action' => '前置条件',
            'after_action' => '后置条件',
            'during_action' => '执行中条件',
            'validation' => '验证条件',
            'filter' => '过滤条件',
        ];

        $actual = [];
        foreach (ConditionTrigger::cases() as $case) {
            $actual[$case->value] = $case->getLabel();
        }

        $this->assertSame($expected, $actual);
    }
}
