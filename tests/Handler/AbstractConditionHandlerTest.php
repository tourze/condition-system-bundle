<?php

namespace Tourze\ConditionSystemBundle\Tests\Handler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Exception\NotImplementedException;
use Tourze\ConditionSystemBundle\Handler\AbstractConditionHandler;
use Tourze\ConditionSystemBundle\Interface\ActorInterface;
use Tourze\ConditionSystemBundle\Interface\ConditionHandlerInterface;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

/**
 * @internal
 */
#[CoversClass(AbstractConditionHandler::class)]
final class AbstractConditionHandlerTest extends TestCase
{
    private AbstractConditionHandler $handler;

    private ConditionInterface&MockObject $mockCondition;

    private EvaluationContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        // 创建抽象类的匿名实现
        $this->handler = new class extends AbstractConditionHandler {
            public function getType(): string
            {
                return 'test_handler';
            }

            public function getLabel(): string
            {
                return '测试处理器';
            }

            public function getDescription(): string
            {
                return '用于测试的处理器';
            }

            public function getFormFields(): iterable
            {
                return [];
            }

            /**
             * @param array<string, mixed> $config
             */
            public function validateConfig(array $config): ValidationResult
            {
                return ValidationResult::success();
            }

            /**
             * @param array<string, mixed> $config
             */
            public function createCondition(SubjectInterface $subject, array $config): ConditionInterface
            {
                throw new NotImplementedException('Not implemented in test');
            }

            /**
             * @param array<string, mixed> $config
             */
            public function updateCondition(ConditionInterface $condition, array $config): void
            {
                // Not implemented in test
            }

            public function getDisplayText(ConditionInterface $condition): string
            {
                return 'Test condition';
            }

            /**
             * @return array<int, ConditionTrigger>
             */
            public function getSupportedTriggers(): array
            {
                return [ConditionTrigger::BEFORE_ACTION];
            }

            protected function doEvaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult
            {
                return EvaluationResult::pass(['test' => true]);
            }
        };

        $this->mockCondition = $this->createMock(ConditionInterface::class);

        $mockActor = $this->createMock(ActorInterface::class);
        $mockActor->expects($this->any())->method('getActorId')->willReturn('test_actor');
        $mockActor->expects($this->any())->method('getActorType')->willReturn('user');
        $mockActor->expects($this->any())->method('getActorData')->willReturn([]);

        $this->context = EvaluationContext::create($mockActor);
    }

    public function testEvaluateCallsDoEvaluateWhenConditionIsEnabled(): void
    {
        $this->mockCondition->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true)
        ;

        $result = $this->handler->evaluate($this->mockCondition, $this->context);

        $this->assertTrue($result->isPassed());
        $this->assertEquals(['test' => true], $result->getMetadata());
    }

    public function testEvaluateReturnsPassWhenConditionIsDisabled(): void
    {
        $this->mockCondition->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false)
        ;

        $result = $this->handler->evaluate($this->mockCondition, $this->context);

        $this->assertTrue($result->isPassed());
        $this->assertEquals(['reason' => 'condition_disabled'], $result->getMetadata());
    }

    public function testEvaluateWithDisabledConditionDoesNotCallDoEvaluate(): void
    {
        // 创建一个可以验证 doEvaluate 是否被调用的处理器
        $handler = new class extends AbstractConditionHandler {
            public bool $doEvaluateCalled = false;

            public function getType(): string
            {
                return 'test';
            }

            public function getLabel(): string
            {
                return 'Test';
            }

            public function getDescription(): string
            {
                return 'Test';
            }

            public function getFormFields(): iterable
            {
                return [];
            }

            /**
             * @param array<string, mixed> $config
             */
            public function validateConfig(array $config): ValidationResult
            {
                return ValidationResult::success();
            }

            /**
             * @param array<string, mixed> $config
             */
            public function createCondition(SubjectInterface $subject, array $config): ConditionInterface
            {
                throw new NotImplementedException('Not implemented');
            }

            /**
             * @param array<string, mixed> $config
             */
            public function updateCondition(ConditionInterface $condition, array $config): void
            {
            }

            public function getDisplayText(ConditionInterface $condition): string
            {
                return 'Test';
            }

            /**
             * @return array<int, ConditionTrigger>
             */
            public function getSupportedTriggers(): array
            {
                return [];
            }

            protected function doEvaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult
            {
                $this->doEvaluateCalled = true;

                return EvaluationResult::pass();
            }
        };

        $this->mockCondition->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false)
        ;

        $handler->evaluate($this->mockCondition, $this->context);

        $this->assertFalse($handler->doEvaluateCalled);
    }

    public function testHandlerImplementsConditionHandlerInterface(): void
    {
        $this->assertInstanceOf(
            ConditionHandlerInterface::class,
            $this->handler
        );
    }

    public function testHandlerBasicMethods(): void
    {
        $this->assertEquals('test_handler', $this->handler->getType());
        $this->assertEquals('测试处理器', $this->handler->getLabel());
        $this->assertEquals('用于测试的处理器', $this->handler->getDescription());
        $this->assertCount(0, iterator_to_array($this->handler->getFormFields()));
        $this->assertCount(1, $this->handler->getSupportedTriggers());
    }

    public function testValidateConfigReturnsValidationResult(): void
    {
        $result = $this->handler->validateConfig(['test' => 'value']);

        $this->assertInstanceOf(
            ValidationResult::class,
            $result
        );
        $this->assertTrue($result->isValid());
    }

    public function testGetDisplayTextReturnsString(): void
    {
        $displayText = $this->handler->getDisplayText($this->mockCondition);
        $this->assertEquals('Test condition', $displayText);
    }

    public function testEvaluateWithDifferentEnabledStates(): void
    {
        // 测试启用状态
        $this->mockCondition->expects($this->exactly(2))
            ->method('isEnabled')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        // 第一次调用 - 条件启用
        $result1 = $this->handler->evaluate($this->mockCondition, $this->context);
        $this->assertTrue($result1->isPassed());
        $this->assertEquals(['test' => true], $result1->getMetadata());

        // 第二次调用 - 条件禁用
        $result2 = $this->handler->evaluate($this->mockCondition, $this->context);
        $this->assertTrue($result2->isPassed());
        $this->assertEquals(['reason' => 'condition_disabled'], $result2->getMetadata());
    }
}
