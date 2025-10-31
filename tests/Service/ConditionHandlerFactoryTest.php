<?php

namespace Tourze\ConditionSystemBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\ConditionSystemBundle\Exception\ConditionHandlerNotFoundException;
use Tourze\ConditionSystemBundle\Service\ConditionHandlerFactory;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ConditionHandlerFactory::class)]
#[RunTestsInSeparateProcesses]
final class ConditionHandlerFactoryTest extends AbstractIntegrationTestCase
{
    private ConditionHandlerFactory $factory;

    protected function onSetUp(): void
    {
        $this->factory = self::getService(ConditionHandlerFactory::class);
    }

    public function testFactoryIsAvailable(): void
    {
        $this->assertInstanceOf(ConditionHandlerFactory::class, $this->factory);
    }

    public function testGetHandlerThrowsExceptionForNonExistentType(): void
    {
        $this->expectException(ConditionHandlerNotFoundException::class);
        $this->expectExceptionMessage('未找到条件处理器: non_existent_type');

        $this->factory->getHandler('non_existent_type');
    }

    public function testHasHandlerWorksCorrectly(): void
    {
        // 测试不存在的类型
        $this->assertFalse($this->factory->hasHandler('non_existent_type'));
        $this->assertFalse($this->factory->hasHandler(''));

        // 如果有注册的处理器，测试存在的类型
        $allHandlers = $this->factory->getAllHandlers();
        if ([] !== $allHandlers) {
            $firstHandlerType = array_key_first($allHandlers);
            $this->assertTrue($this->factory->hasHandler($firstHandlerType));
        }
    }

    public function testGetAllHandlersReturnsArray(): void
    {
        $allHandlers = $this->factory->getAllHandlers();
        $this->assertIsArray($allHandlers);
    }

    public function testGetAvailableTypesReturnsArray(): void
    {
        $availableTypes = $this->factory->getAvailableTypes();
        $this->assertIsArray($availableTypes);

        // 如果有可用类型，验证结构
        foreach ($availableTypes as $type => $info) {
            $this->assertIsString($type);
            $this->assertIsArray($info);
            $this->assertArrayHasKey('type', $info);
            $this->assertArrayHasKey('label', $info);
            $this->assertArrayHasKey('description', $info);
            $this->assertArrayHasKey('supportedTriggers', $info);
            $this->assertEquals($type, $info['type']);
        }
    }

    public function testGetHandlerExceptionMessageContainsType(): void
    {
        $nonExistentType = 'some_random_type';

        try {
            $this->factory->getHandler($nonExistentType);
            self::fail('应该抛出异常');
        } catch (ConditionHandlerNotFoundException $e) {
            $this->assertStringContainsString($nonExistentType, $e->getMessage());
        }
    }
}
