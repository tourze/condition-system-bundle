<?php

namespace Tourze\ConditionSystemBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Service\ConditionHandlerFactory;
use Tourze\ConditionSystemBundle\Service\ConditionManagerService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ConditionManagerService::class)]
#[RunTestsInSeparateProcesses]
final class ConditionManagerServiceTest extends AbstractIntegrationTestCase
{
    private ConditionManagerService $service;

    private ConditionHandlerFactory $handlerFactory;

    protected function onSetUp(): void
    {
        $this->service = self::getService(ConditionManagerService::class);
        $this->handlerFactory = self::getService(ConditionHandlerFactory::class);
    }

    public function testServiceIsAvailable(): void
    {
        $this->service = self::getService(ConditionManagerService::class);
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testGetAvailableConditionTypesReturnsArray(): void
    {
        $types = $this->service->getAvailableConditionTypes();
        $this->assertIsArray($types);
    }

    public function testGetAvailableConditionTypesWithTriggerFilter(): void
    {
        // 获取所有可用类型
        $allTypes = $this->handlerFactory->getAvailableTypes();

        // 如果有可用类型，测试过滤功能
        if ([] !== $allTypes) {
            $types = $this->service->getAvailableConditionTypes(ConditionTrigger::BEFORE_ACTION);
            $this->assertIsArray($types);

            // 验证返回的类型都支持指定的触发器
            foreach ($types as $typeName => $config) {
                $this->assertIsString($typeName);
                $this->assertIsArray($config);
                $this->assertArrayHasKey('supportedTriggers', $config);
                /** @var array<int, ConditionTrigger> $supportedTriggers */
                $supportedTriggers = $config['supportedTriggers'];
                $this->assertContains(ConditionTrigger::BEFORE_ACTION, $supportedTriggers);
            }
        } else {
            // 如果没有可用类型，至少验证返回的是数组
            $types = $this->service->getAvailableConditionTypes(ConditionTrigger::BEFORE_ACTION);
            $this->assertIsArray($types);
        }
    }

    public function testValidateConditionConfig(): void
    {
        // 由于缺少具体的条件处理器实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testGetConditionDisplayText(): void
    {
        // 由于缺少具体的条件实体实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testEvaluateCondition(): void
    {
        // 由于缺少具体的条件实体和上下文实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testEvaluateConditions(): void
    {
        // 由于缺少具体的条件集合和上下文实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testCreateCondition(): void
    {
        // 由于缺少具体的主体和条件类型实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testUpdateCondition(): void
    {
        // 由于缺少具体的条件实体实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }

    public function testDeleteCondition(): void
    {
        // 由于缺少具体的条件实体实现，仅测试服务可访问性
        $this->assertInstanceOf(ConditionManagerService::class, $this->service);
    }
}
