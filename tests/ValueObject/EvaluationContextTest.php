<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Interface\ActorInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;

/**
 * @internal
 */
#[CoversClass(EvaluationContext::class)]
final class EvaluationContextTest extends TestCase
{
    private ActorInterface&MockObject $mockActor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockActor = $this->createMock(ActorInterface::class);
        $this->mockActor->expects($this->any())->method('getActorId')->willReturn('user_123');
        $this->mockActor->expects($this->any())->method('getActorType')->willReturn('user');
        $this->mockActor->expects($this->any())->method('getActorData')->willReturn(['name' => 'Test User']);
    }

    public function testCreateReturnsContextWithActor(): void
    {
        $context = EvaluationContext::create($this->mockActor);

        $this->assertSame($this->mockActor, $context->getActor());
        $this->assertNull($context->getPayload());
        $this->assertEmpty($context->getMetadata());
    }

    public function testWithPayloadReturnsNewContextWithPayload(): void
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

    public function testWithMetadataReturnsNewContextWithMetadata(): void
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

    public function testChainingWithPayloadAndMetadata(): void
    {
        $payload = new \stdClass();
        $payload->id = 123;
        $metadata = ['source' => 'api', 'version' => '1.0'];

        $context = EvaluationContext::create($this->mockActor)
            ->withPayload($payload)
            ->withMetadata($metadata)
        ;

        $this->assertSame($this->mockActor, $context->getActor());
        $this->assertSame($payload, $context->getPayload());
        $this->assertEquals($metadata, $context->getMetadata());
    }

    public function testGetMetadataValueReturnsCorrectValue(): void
    {
        $metadata = ['key1' => 'value1', 'key2' => null, 'key3' => 0];
        $context = EvaluationContext::create($this->mockActor)->withMetadata($metadata);

        $this->assertEquals('value1', $context->getMetadataValue('key1'));
        $this->assertNull($context->getMetadataValue('key2'));
        $this->assertEquals(0, $context->getMetadataValue('key3'));
    }

    public function testGetMetadataValueReturnsDefaultWhenKeyNotExists(): void
    {
        $context = EvaluationContext::create($this->mockActor);

        $this->assertNull($context->getMetadataValue('non_existent_key'));
        $this->assertEquals('default', $context->getMetadataValue('non_existent_key', 'default'));
        $this->assertEquals(42, $context->getMetadataValue('non_existent_key', 42));
    }

    public function testGetMetadataValueWithExistingMetadata(): void
    {
        $metadata = ['existing_key' => 'existing_value'];
        $context = EvaluationContext::create($this->mockActor)->withMetadata($metadata);

        $this->assertEquals('existing_value', $context->getMetadataValue('existing_key'));
        $this->assertEquals('existing_value', $context->getMetadataValue('existing_key', 'default'));
        $this->assertEquals('default', $context->getMetadataValue('non_existent_key', 'default'));
    }

    public function testImmutabilityWithMultipleOperations(): void
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
