<?php

namespace Tourze\ConditionSystemBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Exception\ConditionHandlerNotFoundException;
use Tourze\ConditionSystemBundle\Interface\ConditionHandlerInterface;
use Tourze\ConditionSystemBundle\Service\ConditionHandlerFactory;

class ConditionHandlerFactoryTest extends TestCase
{
    private ConditionHandlerInterface&MockObject $mockHandler1;
    private ConditionHandlerInterface&MockObject $mockHandler2;
    private ConditionHandlerFactory $factory;

    protected function setUp(): void
    {
        $this->mockHandler1 = $this->createMock(ConditionHandlerInterface::class);
        $this->mockHandler1->expects($this->any())
            ->method('getType')
            ->willReturn('handler_type_1');
        $this->mockHandler1->expects($this->any())
            ->method('getLabel')
            ->willReturn('处理器1');
        $this->mockHandler1->expects($this->any())
            ->method('getDescription')
            ->willReturn('第一个处理器');
        $this->mockHandler1->expects($this->any())
            ->method('getSupportedTriggers')
            ->willReturn(['before_action', 'validation']);

        $this->mockHandler2 = $this->createMock(ConditionHandlerInterface::class);
        $this->mockHandler2->expects($this->any())
            ->method('getType')
            ->willReturn('handler_type_2');
        $this->mockHandler2->expects($this->any())
            ->method('getLabel')
            ->willReturn('处理器2');
        $this->mockHandler2->expects($this->any())
            ->method('getDescription')
            ->willReturn('第二个处理器');
        $this->mockHandler2->expects($this->any())
            ->method('getSupportedTriggers')
            ->willReturn(['after_action']);

        $handlers = [$this->mockHandler1, $this->mockHandler2];
        $this->factory = new ConditionHandlerFactory($handlers);
    }

    public function test_constructor_registers_handlers_by_type(): void
    {
        $this->assertTrue($this->factory->hasHandler('handler_type_1'));
        $this->assertTrue($this->factory->hasHandler('handler_type_2'));
        $this->assertFalse($this->factory->hasHandler('non_existent_type'));
    }

    public function test_get_handler_returns_correct_handler(): void
    {
        $handler1 = $this->factory->getHandler('handler_type_1');
        $handler2 = $this->factory->getHandler('handler_type_2');

        $this->assertSame($this->mockHandler1, $handler1);
        $this->assertSame($this->mockHandler2, $handler2);
    }

    public function test_get_handler_throws_exception_for_non_existent_type(): void
    {
        $this->expectException(ConditionHandlerNotFoundException::class);
        $this->expectExceptionMessage('未找到条件处理器: non_existent_type');

        $this->factory->getHandler('non_existent_type');
    }

    public function test_has_handler_returns_correct_boolean(): void
    {
        $this->assertTrue($this->factory->hasHandler('handler_type_1'));
        $this->assertTrue($this->factory->hasHandler('handler_type_2'));
        $this->assertFalse($this->factory->hasHandler('handler_type_3'));
        $this->assertFalse($this->factory->hasHandler(''));
    }

    public function test_get_all_handlers_returns_all_registered_handlers(): void
    {
        $allHandlers = $this->factory->getAllHandlers();

        $this->assertCount(2, $allHandlers);
        $this->assertArrayHasKey('handler_type_1', $allHandlers);
        $this->assertArrayHasKey('handler_type_2', $allHandlers);
        $this->assertSame($this->mockHandler1, $allHandlers['handler_type_1']);
        $this->assertSame($this->mockHandler2, $allHandlers['handler_type_2']);
    }

    public function test_get_available_types_returns_handler_information(): void
    {
        $availableTypes = $this->factory->getAvailableTypes();

        $this->assertCount(2, $availableTypes);
        $this->assertArrayHasKey('handler_type_1', $availableTypes);
        $this->assertArrayHasKey('handler_type_2', $availableTypes);

        $type1Info = $availableTypes['handler_type_1'];
        $this->assertEquals('handler_type_1', $type1Info['type']);
        $this->assertEquals('处理器1', $type1Info['label']);
        $this->assertEquals('第一个处理器', $type1Info['description']);
        $this->assertEquals(['before_action', 'validation'], $type1Info['supportedTriggers']);

        $type2Info = $availableTypes['handler_type_2'];
        $this->assertEquals('handler_type_2', $type2Info['type']);
        $this->assertEquals('处理器2', $type2Info['label']);
        $this->assertEquals('第二个处理器', $type2Info['description']);
        $this->assertEquals(['after_action'], $type2Info['supportedTriggers']);
    }

    public function test_constructor_with_empty_handlers(): void
    {
        $factory = new ConditionHandlerFactory([]);

        $this->assertEmpty($factory->getAllHandlers());
        $this->assertEmpty($factory->getAvailableTypes());
        $this->assertFalse($factory->hasHandler('any_type'));
    }

    public function test_constructor_ignores_non_handler_objects(): void
    {
        $nonHandler = new \stdClass();
        $handlers = [$this->mockHandler1, $nonHandler, $this->mockHandler2];
        
        $factory = new ConditionHandlerFactory($handlers);

        // 应该只注册实际的处理器
        $this->assertCount(2, $factory->getAllHandlers());
        $this->assertTrue($factory->hasHandler('handler_type_1'));
        $this->assertTrue($factory->hasHandler('handler_type_2'));
    }

    public function test_get_handler_exception_message_contains_type(): void
    {
        $nonExistentType = 'some_random_type';
        
        try {
            $this->factory->getHandler($nonExistentType);
            $this->fail('应该抛出异常');
        } catch (ConditionHandlerNotFoundException $e) {
            $this->assertStringContainsString($nonExistentType, $e->getMessage());
        }
    }
} 