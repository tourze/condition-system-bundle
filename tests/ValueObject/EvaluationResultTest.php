<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;

class EvaluationResultTest extends TestCase
{
    public function test_pass_creates_successful_result(): void
    {
        $result = EvaluationResult::pass();
        
        $this->assertTrue($result->isPassed());
        $this->assertEmpty($result->getMessages());
        $this->assertEmpty($result->getMetadata());
        $this->assertNull($result->getFirstMessage());
    }

    public function test_pass_with_metadata(): void
    {
        $metadata = ['execution_time' => 0.5, 'cache_hit' => true];
        $result = EvaluationResult::pass($metadata);
        
        $this->assertTrue($result->isPassed());
        $this->assertEmpty($result->getMessages());
        $this->assertEquals($metadata, $result->getMetadata());
        $this->assertEquals(0.5, $result->getMetadataValue('execution_time'));
        $this->assertTrue($result->getMetadataValue('cache_hit'));
    }

    public function test_fail_creates_failed_result(): void
    {
        $messages = ['条件不满足', '权限不足'];
        $result = EvaluationResult::fail($messages);
        
        $this->assertFalse($result->isPassed());
        $this->assertEquals($messages, $result->getMessages());
        $this->assertEmpty($result->getMetadata());
        $this->assertEquals('条件不满足', $result->getFirstMessage());
    }

    public function test_fail_with_metadata(): void
    {
        $messages = ['验证失败'];
        $metadata = ['error_code' => 'VALIDATION_FAILED', 'timestamp' => time()];
        $result = EvaluationResult::fail($messages, $metadata);
        
        $this->assertFalse($result->isPassed());
        $this->assertEquals($messages, $result->getMessages());
        $this->assertEquals($metadata, $result->getMetadata());
        $this->assertEquals('VALIDATION_FAILED', $result->getMetadataValue('error_code'));
    }

    public function test_fail_with_empty_messages(): void
    {
        $result = EvaluationResult::fail([]);
        
        $this->assertFalse($result->isPassed());
        $this->assertEmpty($result->getMessages());
        $this->assertNull($result->getFirstMessage());
    }

    public function test_get_first_message_returns_null_when_no_messages(): void
    {
        $result = EvaluationResult::pass();
        
        $this->assertNull($result->getFirstMessage());
    }

    public function test_get_first_message_returns_first_message_when_multiple(): void
    {
        $messages = ['第一个消息', '第二个消息', '第三个消息'];
        $result = EvaluationResult::fail($messages);
        
        $this->assertEquals('第一个消息', $result->getFirstMessage());
    }

    public function test_get_metadata_value_returns_default_when_key_not_exists(): void
    {
        $result = EvaluationResult::pass();
        
        $this->assertNull($result->getMetadataValue('non_existent_key'));
        $this->assertEquals('default_value', $result->getMetadataValue('non_existent_key', 'default_value'));
    }

    public function test_get_metadata_value_returns_actual_value_when_key_exists(): void
    {
        $metadata = ['key1' => 'value1', 'key2' => 42];
        $result = EvaluationResult::pass($metadata);
        
        $this->assertEquals('value1', $result->getMetadataValue('key1'));
        $this->assertEquals(42, $result->getMetadataValue('key2'));
        $this->assertEquals('value1', $result->getMetadataValue('key1', 'default'));
    }

    public function test_get_metadata_value_handles_null_values(): void
    {
        $metadata = ['null_key' => null];
        $result = EvaluationResult::pass($metadata);
        
        // 由于使用了 ?? 操作符，null 值会返回默认值
        $this->assertNull($result->getMetadataValue('null_key'));
        $this->assertEquals('default', $result->getMetadataValue('null_key', 'default'));
    }
} 