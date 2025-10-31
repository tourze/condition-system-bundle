<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

/**
 * @internal
 */
#[CoversClass(ValidationResult::class)]
final class ValidationResultTest extends TestCase
{
    public function testSuccessCreatesValidResult(): void
    {
        $result = ValidationResult::success();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNull($result->getFirstError());
    }

    public function testFailureCreatesInvalidResultWithErrors(): void
    {
        $errors = ['字段不能为空', '格式不正确'];
        $result = ValidationResult::failure($errors);

        $this->assertFalse($result->isValid());
        $this->assertEquals($errors, $result->getErrors());
        $this->assertEquals('字段不能为空', $result->getFirstError());
    }

    public function testFailureWithEmptyErrorsArray(): void
    {
        $result = ValidationResult::failure([]);

        $this->assertFalse($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNull($result->getFirstError());
    }

    public function testFailureWithSingleError(): void
    {
        $error = '单个错误信息';
        $result = ValidationResult::failure([$error]);

        $this->assertFalse($result->isValid());
        $this->assertEquals([$error], $result->getErrors());
        $this->assertEquals($error, $result->getFirstError());
    }

    public function testGetFirstErrorReturnsNullWhenNoErrors(): void
    {
        $result = ValidationResult::success();

        $this->assertNull($result->getFirstError());
    }

    public function testGetFirstErrorReturnsFirstErrorWhenMultipleErrors(): void
    {
        $errors = ['第一个错误', '第二个错误', '第三个错误'];
        $result = ValidationResult::failure($errors);

        $this->assertEquals('第一个错误', $result->getFirstError());
    }
}
