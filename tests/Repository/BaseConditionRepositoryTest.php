<?php

namespace Tourze\ConditionSystemBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\ConditionSystemBundle\Entity\BaseCondition;
use Tourze\ConditionSystemBundle\Entity\TestCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Repository\BaseConditionRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(BaseConditionRepository::class)]
#[RunTestsInSeparateProcesses]
final class BaseConditionRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 基础仓库测试不需要特殊设置
    }

    protected function getRepository(): BaseConditionRepository
    {
        return self::getService(BaseConditionRepository::class);
    }

    protected function createNewEntity(): object
    {
        $condition = new TestCondition();
        $condition->setType('test_type');
        $condition->setLabel('测试条件');
        $condition->setTriggerType(ConditionTrigger::BEFORE_ACTION);

        return $condition;
    }

    private function createTestCondition(string $type = 'test_type', string $label = '测试条件'): BaseCondition
    {
        $condition = new TestCondition();
        $condition->setType($type);
        $condition->setLabel($label);
        $condition->setTriggerType(ConditionTrigger::BEFORE_ACTION);

        return $condition;
    }

    public function testFindByTypeReturnsArray(): void
    {
        $repository = $this->getRepository();

        // 创建不同类型的条件
        $condition1 = $this->createTestCondition('specific_type', 'Specific Condition');
        $condition2 = $this->createTestCondition('other_type', 'Other Condition');

        $repository->save($condition1);
        $repository->save($condition2);

        $result = $repository->findByType('specific_type');

        $this->assertIsArray($result);

        foreach ($result as $condition) {
            $this->assertInstanceOf(BaseCondition::class, $condition);
            $this->assertEquals('specific_type', $condition->getType());
        }
    }

    public function testFindEnabledReturnsArray(): void
    {
        $repository = $this->getRepository();

        // 创建启用和禁用的条件
        $enabledCondition = $this->createTestCondition('enabled_type', 'Enabled Condition');
        $disabledCondition = $this->createTestCondition('disabled_type', 'Disabled Condition');
        $disabledCondition->setEnabled(false);

        $repository->save($enabledCondition);
        $repository->save($disabledCondition);

        $result = $repository->findEnabled();

        $this->assertIsArray($result);

        foreach ($result as $condition) {
            $this->assertInstanceOf(BaseCondition::class, $condition);
            $this->assertTrue($condition->isEnabled());
        }
    }

    public function testFindEnabledByTypeReturnsArray(): void
    {
        $repository = $this->getRepository();

        // 创建不同状态和类型的条件
        $enabledCondition = $this->createTestCondition('target_type', 'Enabled Target');
        $disabledCondition = $this->createTestCondition('target_type', 'Disabled Target');
        $disabledCondition->setEnabled(false);
        $otherCondition = $this->createTestCondition('other_type', 'Other Enabled');

        $repository->save($enabledCondition);
        $repository->save($disabledCondition);
        $repository->save($otherCondition);

        $result = $repository->findEnabledByType('target_type');

        $this->assertIsArray($result);

        foreach ($result as $condition) {
            $this->assertInstanceOf(BaseCondition::class, $condition);
            $this->assertEquals('target_type', $condition->getType());
            $this->assertTrue($condition->isEnabled());
        }
    }

    public function testSaveMethodWorks(): void
    {
        $repository = $this->getRepository();
        $condition = $this->createTestCondition('save_test', 'Save Test Condition');

        // 测试 save 方法是否正常工作
        $repository->save($condition);

        $this->assertNotNull($condition->getId());

        // 验证已保存的实体可以被查找到
        $foundCondition = $repository->find($condition->getId());
        $this->assertNotNull($foundCondition);
        $this->assertInstanceOf(BaseCondition::class, $foundCondition);
        $this->assertEquals('save_test', $foundCondition->getType());
    }

    public function testRemoveMethodWorks(): void
    {
        $repository = $this->getRepository();
        $condition = $this->createTestCondition('remove_test', 'Remove Test Condition');

        // 先保存实体
        $repository->save($condition);
        $conditionId = $condition->getId();
        $this->assertNotNull($conditionId);

        // 验证实体已保存
        $this->assertNotNull($repository->find($conditionId));

        // 删除实体
        $repository->remove($condition);

        // 验证实体已删除
        $this->assertNull($repository->find($conditionId));
    }
}
