<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Exception\ConditionHandlerNotFoundException;
use Tourze\ConditionSystemBundle\Exception\ConditionSystemException;

class ConditionHandlerNotFoundExceptionTest extends TestCase
{
    public function test_exception_extends_condition_system_exception(): void
    {
        $exception = new ConditionHandlerNotFoundException();
        
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_exception_with_handler_type_message(): void
    {
        $handlerType = 'user_permission';
        $message = "未找到条件处理器: {$handlerType}";
        $exception = new ConditionHandlerNotFoundException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertStringContainsString($handlerType, $exception->getMessage());
    }

    public function test_exception_with_code_and_previous(): void
    {
        $previousException = new \InvalidArgumentException('参数错误');
        $exception = new ConditionHandlerNotFoundException('处理器未找到', 404, $previousException);
        
        $this->assertEquals('处理器未找到', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function test_exception_can_be_thrown_and_caught_as_specific_type(): void
    {
        $this->expectException(ConditionHandlerNotFoundException::class);
        $this->expectExceptionMessage('特定处理器未找到');
        
        throw new ConditionHandlerNotFoundException('特定处理器未找到');
    }

    public function test_exception_can_be_caught_as_parent_type(): void
    {
        $this->expectException(ConditionSystemException::class);
        
        throw new ConditionHandlerNotFoundException('处理器未找到');
    }

    public function test_exception_inheritance_chain(): void
    {
        $exception = new ConditionHandlerNotFoundException();
        
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(ConditionHandlerNotFoundException::class, $exception);
    }

    public function test_exception_with_empty_message(): void
    {
        $exception = new ConditionHandlerNotFoundException();
        
        $this->assertEquals('', $exception->getMessage());
    }

    public function test_exception_context_information(): void
    {
        $handlerType = 'complex_condition_handler';
        $message = "条件处理器 '{$handlerType}' 未注册或不存在";
        $exception = new ConditionHandlerNotFoundException($message);
        
        $this->assertStringContainsString($handlerType, $exception->getMessage());
        $this->assertStringContainsString('未注册', $exception->getMessage());
    }
} 