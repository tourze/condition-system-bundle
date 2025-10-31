<?php

namespace Tourze\ConditionSystemBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\ConditionSystemBundle\Entity\TestCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;

/**
 * 测试条件测试数据
 */
class TestConditionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建基础测试条件
        for ($i = 1; $i <= 5; ++$i) {
            $condition = new TestCondition();
            $condition->setType("test_type_{$i}");
            $condition->setLabel("测试条件 {$i}");
            $condition->setRemark("这是第 {$i} 个测试条件");
            $condition->setEnabled(1 === $i % 2); // 奇数启用，偶数禁用
            $condition->setTriggerType(ConditionTrigger::BEFORE_ACTION);

            $manager->persist($condition);
        }

        // 创建不同触发器类型的条件
        foreach (ConditionTrigger::cases() as $trigger) {
            $condition = new TestCondition();
            $condition->setType("trigger_type_{$trigger->value}");
            $condition->setLabel("触发器条件 {$trigger->getLabel()}");
            $condition->setRemark("触发器类型: {$trigger->value}");
            $condition->setEnabled(true);
            $condition->setTriggerType($trigger);

            $manager->persist($condition);
        }

        // 创建特定状态的条件用于测试
        $enabledCondition = new TestCondition();
        $enabledCondition->setType('enabled_test');
        $enabledCondition->setLabel('启用的条件');
        $enabledCondition->setRemark('用于测试启用状态的条件');
        $enabledCondition->setEnabled(true);
        $enabledCondition->setTriggerType(ConditionTrigger::AFTER_ACTION);
        $manager->persist($enabledCondition);

        $disabledCondition = new TestCondition();
        $disabledCondition->setType('disabled_test');
        $disabledCondition->setLabel('禁用的条件');
        $disabledCondition->setRemark('用于测试禁用状态的条件');
        $disabledCondition->setEnabled(false);
        $disabledCondition->setTriggerType(ConditionTrigger::VALIDATION);
        $manager->persist($disabledCondition);

        $manager->flush();
    }
}
