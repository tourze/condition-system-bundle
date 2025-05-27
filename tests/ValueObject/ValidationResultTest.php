<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

class ValidationResultTest extends TestCase
{
    public function test_success_creates_valid_result(): void
    {
        $result = ValidationResult::success();
        
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNull($result->getFirstError());
    }

    public function test_failure_creates_invalid_result_with_errors(): void
    {
        $errors = ['字段不能为空', '格式不正确'];
        $result = ValidationResult::failure($errors);
        
        $this->assertFalse($result->isValid());
        $this->assertEquals($errors, $result->getErrors());
        $this->assertEquals('字段不能为空', $result->getFirstError());
    }

    public function test_failure_with_empty_errors_array(): void
    {
        $result = ValidationResult::failure([]);
        
        $this->assertFalse($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNull($result->getFirstError());
    }

    public function test_failure_with_single_error(): void
    {
        $error = '单个错误信息';
        $result = ValidationResult::failure([$error]);
        
        $this->assertFalse($result->isValid());
        $this->assertEquals([$error], $result->getErrors());
        $this->assertEquals($error, $result->getFirstError());
    }

    public function test_get_first_error_returns_null_when_no_errors(): void
    {
        $result = ValidationResult::success();
        
        $this->assertNull($result->getFirstError());
    }

    public function test_get_first_error_returns_first_error_when_multiple_errors(): void
    {
        $errors = ['第一个错误', '第二个错误', '第三个错误'];
        $result = ValidationResult::failure($errors);
        
        $this->assertEquals('第一个错误', $result->getFirstError());
    }
} 