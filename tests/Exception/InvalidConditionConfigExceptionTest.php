<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Exception\ConditionSystemException;
use Tourze\ConditionSystemBundle\Exception\InvalidConditionConfigException;

class InvalidConditionConfigExceptionTest extends TestCase
{
    public function test_exception_extends_condition_system_exception(): void
    {
        $exception = new InvalidConditionConfigException();
        
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_exception_with_config_error_message(): void
    {
        $message = '条件配置无效：缺少必需字段 "type"';
        $exception = new InvalidConditionConfigException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertStringContainsString('条件配置无效', $exception->getMessage());
    }

    public function test_exception_with_validation_errors(): void
    {
        $errors = ['字段 "value" 不能为空', '字段 "operator" 必须是有效的操作符'];
        $message = implode('; ', $errors);
        $exception = new InvalidConditionConfigException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertStringContainsString('字段 "value" 不能为空', $exception->getMessage());
        $this->assertStringContainsString('字段 "operator" 必须是有效的操作符', $exception->getMessage());
    }

    public function test_exception_with_code_and_previous(): void
    {
        $previousException = new \InvalidArgumentException('配置参数错误');
        $exception = new InvalidConditionConfigException('配置验证失败', 400, $previousException);
        
        $this->assertEquals('配置验证失败', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function test_exception_can_be_thrown_and_caught_as_specific_type(): void
    {
        $this->expectException(InvalidConditionConfigException::class);
        $this->expectExceptionMessage('配置格式错误');
        
        throw new InvalidConditionConfigException('配置格式错误');
    }

    public function test_exception_can_be_caught_as_parent_type(): void
    {
        $this->expectException(ConditionSystemException::class);
        
        throw new InvalidConditionConfigException('配置无效');
    }

    public function test_exception_inheritance_chain(): void
    {
        $exception = new InvalidConditionConfigException();
        
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(InvalidConditionConfigException::class, $exception);
    }

    public function test_exception_with_empty_message(): void
    {
        $exception = new InvalidConditionConfigException();
        
        $this->assertEquals('', $exception->getMessage());
    }

    public function test_exception_with_detailed_config_context(): void
    {
        $configField = 'min_value';
        $configValue = 'invalid_number';
        $message = "配置字段 '{$configField}' 的值 '{$configValue}' 无效：必须是数字";
        $exception = new InvalidConditionConfigException($message);
        
        $this->assertStringContainsString($configField, $exception->getMessage());
        $this->assertStringContainsString($configValue, $exception->getMessage());
        $this->assertStringContainsString('必须是数字', $exception->getMessage());
    }

    public function test_exception_with_multiple_validation_errors(): void
    {
        $errors = [
            '字段 "operator" 不能为空',
            '字段 "value" 格式不正确',
            '字段 "target" 必须是字符串'
        ];
        $message = '配置验证失败：' . implode('; ', $errors);
        $exception = new InvalidConditionConfigException($message);
        
        foreach ($errors as $error) {
            $this->assertStringContainsString($error, $exception->getMessage());
        }
    }
} 