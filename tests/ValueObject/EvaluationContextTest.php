<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Interface\ActorInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;

class EvaluationContextTest extends TestCase
{
    private ActorInterface&MockObject $mockActor;

    protected function setUp(): void
    {
        $this->mockActor = $this->createMock(ActorInterface::class);
        $this->mockActor->expects($this->any())->method('getActorId')->willReturn('user_123');
        $this->mockActor->expects($this->any())->method('getActorType')->willReturn('user');
        $this->mockActor->expects($this->any())->method('getActorData')->willReturn(['name' => 'Test User']);
    }

    public function test_create_returns_context_with_actor(): void
    {
        $context = EvaluationContext::create($this->mockActor);
        
        $this->assertSame($this->mockActor, $context->getActor());
        $this->assertNull($context->getPayload());
        $this->assertEmpty($context->getMetadata());
    }

    public function test_with_payload_returns_new_context_with_payload(): void
    {
        $payload = new \stdClass();
        $payload->data = 'test_data';
        
        $originalContext = EvaluationContext::create($this->mockActor);
        $newContext = $originalContext->withPayload($payload);
        
        // 验证不可变性
        $this->assertNotSame($originalContext, $newContext);
        $this->assertNull($originalContext->getPayload());
        $this->assertSame($payload, $newContext->getPayload());
        $this->assertSame($this->mockActor, $newContext->getActor());
    }

    public function test_with_metadata_returns_new_context_with_metadata(): void
    {
        $metadata = ['key1' => 'value1', 'key2' => 42];
        
        $originalContext = EvaluationContext::create($this->mockActor);
        $newContext = $originalContext->withMetadata($metadata);
        
        // 验证不可变性
        $this->assertNotSame($originalContext, $newContext);
        $this->assertEmpty($originalContext->getMetadata());
        $this->assertEquals($metadata, $newContext->getMetadata());
        $this->assertSame($this->mockActor, $newContext->getActor());
    }

    public function test_chaining_with_payload_and_metadata(): void
    {
        $payload = new \stdClass();
        $payload->id = 123;
        $metadata = ['source' => 'api', 'version' => '1.0'];
        
        $context = EvaluationContext::create($this->mockActor)
            ->withPayload($payload)
            ->withMetadata($metadata);
        
        $this->assertSame($this->mockActor, $context->getActor());
        $this->assertSame($payload, $context->getPayload());
        $this->assertEquals($metadata, $context->getMetadata());
    }

    public function test_get_metadata_value_returns_correct_value(): void
    {
        $metadata = ['key1' => 'value1', 'key2' => null, 'key3' => 0];
        $context = EvaluationContext::create($this->mockActor)->withMetadata($metadata);
        
        $this->assertEquals('value1', $context->getMetadataValue('key1'));
        $this->assertNull($context->getMetadataValue('key2'));
        $this->assertEquals(0, $context->getMetadataValue('key3'));
    }

    public function test_get_metadata_value_returns_default_when_key_not_exists(): void
    {
        $context = EvaluationContext::create($this->mockActor);
        
        $this->assertNull($context->getMetadataValue('non_existent_key'));
        $this->assertEquals('default', $context->getMetadataValue('non_existent_key', 'default'));
        $this->assertEquals(42, $context->getMetadataValue('non_existent_key', 42));
    }

    public function test_get_metadata_value_with_existing_metadata(): void
    {
        $metadata = ['existing_key' => 'existing_value'];
        $context = EvaluationContext::create($this->mockActor)->withMetadata($metadata);
        
        $this->assertEquals('existing_value', $context->getMetadataValue('existing_key'));
        $this->assertEquals('existing_value', $context->getMetadataValue('existing_key', 'default'));
        $this->assertEquals('default', $context->getMetadataValue('non_existent_key', 'default'));
    }

    public function test_immutability_with_multiple_operations(): void
    {
        $originalContext = EvaluationContext::create($this->mockActor);
        
        $payload1 = new \stdClass();
        $payload1->id = 1;
        $context1 = $originalContext->withPayload($payload1);
        
        $metadata1 = ['step' => 1];
        $context2 = $context1->withMetadata($metadata1);
        
        $payload2 = new \stdClass();
        $payload2->id = 2;
        $context3 = $context2->withPayload($payload2);
        
        // 验证每个上下文都是独立的
        $this->assertNull($originalContext->getPayload());
        $this->assertEmpty($originalContext->getMetadata());
        
        $this->assertSame($payload1, $context1->getPayload());
        $this->assertEmpty($context1->getMetadata());
        
        $this->assertSame($payload1, $context2->getPayload());
        $this->assertEquals($metadata1, $context2->getMetadata());
        
        $this->assertSame($payload2, $context3->getPayload());
        $this->assertEquals($metadata1, $context3->getMetadata());
    }
} 