<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Exception\ConditionSystemException;

class ConditionSystemExceptionTest extends TestCase
{
    public function test_exception_extends_base_exception(): void
    {
        $exception = new ConditionSystemException();
        
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_exception_with_message(): void
    {
        $message = '条件系统发生错误';
        $exception = new ConditionSystemException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function test_exception_with_message_and_code(): void
    {
        $message = '条件系统错误';
        $code = 1001;
        $exception = new ConditionSystemException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function test_exception_with_previous_exception(): void
    {
        $previousException = new \RuntimeException('原始错误');
        $exception = new ConditionSystemException('条件系统错误', 0, $previousException);
        
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function test_exception_can_be_thrown_and_caught(): void
    {
        $this->expectException(ConditionSystemException::class);
        $this->expectExceptionMessage('测试异常');
        
        throw new ConditionSystemException('测试异常');
    }

    public function test_exception_inheritance_chain(): void
    {
        $exception = new ConditionSystemException();
        
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
    }

    public function test_exception_default_values(): void
    {
        $exception = new ConditionSystemException();
        
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function test_exception_stack_trace(): void
    {
        $exception = new ConditionSystemException('测试');
        
        $this->assertNotEmpty($exception->getTrace());
        $this->assertNotEmpty($exception->getTraceAsString());
        $this->assertStringContainsString(__CLASS__, $exception->getTraceAsString());
    }
} 