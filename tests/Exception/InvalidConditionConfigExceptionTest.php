<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Exception\ConditionSystemException;
use Tourze\ConditionSystemBundle\Exception\InvalidConditionConfigException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidConditionConfigException::class)]
final class InvalidConditionConfigExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsConditionSystemException(): void
    {
        $exception = new InvalidConditionConfigException();

        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithConfigErrorMessage(): void
    {
        $message = '条件配置无效：缺少必需字段 "type"';
        $exception = new InvalidConditionConfigException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertStringContainsString('条件配置无效', $exception->getMessage());
    }

    public function testExceptionWithValidationErrors(): void
    {
        $errors = ['字段 "value" 不能为空', '字段 "operator" 必须是有效的操作符'];
        $message = implode('; ', $errors);
        $exception = new InvalidConditionConfigException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertStringContainsString('字段 "value" 不能为空', $exception->getMessage());
        $this->assertStringContainsString('字段 "operator" 必须是有效的操作符', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previousException = new \InvalidArgumentException('配置参数错误');
        $exception = new InvalidConditionConfigException('配置验证失败', 400, $previousException);

        $this->assertEquals('配置验证失败', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testExceptionCanBeThrownAndCaughtAsSpecificType(): void
    {
        $this->expectException(InvalidConditionConfigException::class);
        $this->expectExceptionMessage('配置格式错误');

        throw new InvalidConditionConfigException('配置格式错误');
    }

    public function testExceptionCanBeCaughtAsParentType(): void
    {
        $this->expectException(ConditionSystemException::class);

        throw new InvalidConditionConfigException('配置无效');
    }

    public function testExceptionInheritanceChain(): void
    {
        $exception = new InvalidConditionConfigException();

        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
        $this->assertInstanceOf(InvalidConditionConfigException::class, $exception);
    }

    public function testExceptionWithEmptyMessage(): void
    {
        $exception = new InvalidConditionConfigException();

        $this->assertEquals('', $exception->getMessage());
    }

    public function testExceptionWithDetailedConfigContext(): void
    {
        $configField = 'min_value';
        $configValue = 'invalid_number';
        $message = "配置字段 '{$configField}' 的值 '{$configValue}' 无效：必须是数字";
        $exception = new InvalidConditionConfigException($message);

        $this->assertStringContainsString($configField, $exception->getMessage());
        $this->assertStringContainsString($configValue, $exception->getMessage());
        $this->assertStringContainsString('必须是数字', $exception->getMessage());
    }

    public function testExceptionWithMultipleValidationErrors(): void
    {
        $errors = [
            '字段 "operator" 不能为空',
            '字段 "value" 格式不正确',
            '字段 "target" 必须是字符串',
        ];
        $message = '配置验证失败：' . implode('; ', $errors);
        $exception = new InvalidConditionConfigException($message);

        foreach ($errors as $error) {
            $this->assertStringContainsString($error, $exception->getMessage());
        }
    }
}
