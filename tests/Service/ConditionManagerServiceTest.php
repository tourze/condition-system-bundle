<?php

namespace Tourze\ConditionSystemBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Exception\InvalidConditionConfigException;
use Tourze\ConditionSystemBundle\Interface\ActorInterface;
use Tourze\ConditionSystemBundle\Interface\ConditionHandlerInterface;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;
use Tourze\ConditionSystemBundle\Service\ConditionHandlerFactory;
use Tourze\ConditionSystemBundle\Service\ConditionManagerService;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

class ConditionManagerServiceTest extends TestCase
{
    private ConditionManagerService $service;
    private ConditionHandlerFactory&MockObject $mockHandlerFactory;
    private EntityManagerInterface&MockObject $mockEntityManager;
    private LoggerInterface&MockObject $mockLogger;
    private ConditionHandlerInterface&MockObject $mockHandler;
    private ConditionInterface&MockObject $mockCondition;
    private SubjectInterface&MockObject $mockSubject;
    private EvaluationContext $context;

    protected function setUp(): void
    {
        $this->mockHandlerFactory = $this->createMock(ConditionHandlerFactory::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockHandler = $this->createMock(ConditionHandlerInterface::class);
        $this->mockCondition = $this->createMock(ConditionInterface::class);
        $this->mockSubject = $this->createMock(SubjectInterface::class);

        $this->service = new ConditionManagerService(
            $this->mockHandlerFactory,
            $this->mockEntityManager,
            $this->mockLogger
        );

        /** @var ActorInterface&MockObject $mockActor */
        $mockActor = $this->createMock(ActorInterface::class);
        $mockActor->expects($this->any())->method('getActorId')->willReturn('test_actor');
        $mockActor->expects($this->any())->method('getActorType')->willReturn('user');
        $mockActor->expects($this->any())->method('getActorData')->willReturn([]);
        
        $this->context = EvaluationContext::create($mockActor);
    }

    public function test_create_condition_with_valid_config(): void
    {
        $type = 'test_type';
        $config = ['key' => 'value'];
        
        $this->mockSubject->expects($this->any())
            ->method('getSubjectId')
            ->willReturn('subject_123');
        $this->mockSubject->expects($this->any())
            ->method('getSubjectType')
            ->willReturn('user');

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($type)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('validateConfig')
            ->with($config)
            ->willReturn(ValidationResult::success());

        $this->mockHandler->expects($this->once())
            ->method('createCondition')
            ->with($this->mockSubject, $config)
            ->willReturn($this->mockCondition);

        $this->mockCondition->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->mockEntityManager->expects($this->once())
            ->method('persist')
            ->with($this->mockCondition);

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->mockLogger->expects($this->once())
            ->method('info')
            ->with('条件创建成功', $this->isType('array'));

        $result = $this->service->createCondition($this->mockSubject, $type, $config);

        $this->assertSame($this->mockCondition, $result);
    }

    public function test_create_condition_with_invalid_config_throws_exception(): void
    {
        $type = 'test_type';
        $config = ['invalid' => 'config'];
        $errors = ['配置无效', '缺少必需字段'];

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($type)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('validateConfig')
            ->with($config)
            ->willReturn(ValidationResult::failure($errors));

        $this->expectException(InvalidConditionConfigException::class);
        $this->expectExceptionMessage('配置无效; 缺少必需字段');

        $this->service->createCondition($this->mockSubject, $type, $config);
    }

    public function test_update_condition_with_valid_config(): void
    {
        $config = ['updated' => 'value'];
        $conditionType = 'test_type';

        $this->mockCondition->expects($this->any())
            ->method('getType')
            ->willReturn($conditionType);
        $this->mockCondition->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($conditionType)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('validateConfig')
            ->with($config)
            ->willReturn(ValidationResult::success());

        $this->mockHandler->expects($this->once())
            ->method('updateCondition')
            ->with($this->mockCondition, $config);

        $this->mockEntityManager->expects($this->once())
            ->method('persist')
            ->with($this->mockCondition);

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->mockLogger->expects($this->once())
            ->method('info')
            ->with('条件更新成功', $this->isType('array'));

        $this->service->updateCondition($this->mockCondition, $config);
    }

    public function test_update_condition_with_invalid_config_throws_exception(): void
    {
        $config = ['invalid' => 'config'];
        $conditionType = 'test_type';
        $errors = ['更新配置无效'];

        $this->mockCondition->expects($this->any())
            ->method('getType')
            ->willReturn($conditionType);

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($conditionType)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('validateConfig')
            ->with($config)
            ->willReturn(ValidationResult::failure($errors));

        $this->expectException(InvalidConditionConfigException::class);
        $this->expectExceptionMessage('更新配置无效');

        $this->service->updateCondition($this->mockCondition, $config);
    }

    public function test_delete_condition(): void
    {
        $this->mockCondition->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $this->mockCondition->expects($this->any())
            ->method('getType')
            ->willReturn('test_type');

        $this->mockEntityManager->expects($this->once())
            ->method('remove')
            ->with($this->mockCondition);

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->mockLogger->expects($this->once())
            ->method('info')
            ->with('条件删除成功', $this->isType('array'));

        $this->service->deleteCondition($this->mockCondition);
    }

    public function test_evaluate_condition_success(): void
    {
        $conditionType = 'test_type';
        $expectedResult = EvaluationResult::pass(['test' => true]);

        $this->mockCondition->expects($this->any())
            ->method('getType')
            ->willReturn($conditionType);
        $this->mockCondition->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($conditionType)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('evaluate')
            ->with($this->mockCondition, $this->context)
            ->willReturn($expectedResult);

        $this->mockLogger->expects($this->once())
            ->method('debug')
            ->with('条件评估完成', $this->isType('array'));

        $result = $this->service->evaluateCondition($this->mockCondition, $this->context);

        $this->assertSame($expectedResult, $result);
    }

    public function test_evaluate_condition_handles_exception(): void
    {
        $conditionType = 'test_type';
        $exception = new \RuntimeException('评估失败');

        $this->mockCondition->expects($this->any())
            ->method('getType')
            ->willReturn($conditionType);
        $this->mockCondition->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($conditionType)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('evaluate')
            ->with($this->mockCondition, $this->context)
            ->willThrowException($exception);

        $this->mockLogger->expects($this->once())
            ->method('error')
            ->with('条件评估失败', $this->isType('array'));

        $result = $this->service->evaluateCondition($this->mockCondition, $this->context);

        $this->assertFalse($result->isPassed());
        $this->assertEquals(['系统错误，请稍后重试'], $result->getMessages());
    }

    public function test_evaluate_conditions_all_pass(): void
    {
        $condition1 = $this->createMock(ConditionInterface::class);
        $condition2 = $this->createMock(ConditionInterface::class);
        $conditions = [$condition1, $condition2];

        $condition1->expects($this->any())->method('getType')->willReturn('type1');
        $condition1->expects($this->any())->method('getId')->willReturn(1);
        $condition2->expects($this->any())->method('getType')->willReturn('type2');
        $condition2->expects($this->any())->method('getId')->willReturn(2);

        $handler1 = $this->createMock(ConditionHandlerInterface::class);
        $handler2 = $this->createMock(ConditionHandlerInterface::class);

        $this->mockHandlerFactory->expects($this->exactly(2))
            ->method('getHandler')
            ->willReturnMap([
                ['type1', $handler1],
                ['type2', $handler2],
            ]);

        $handler1->expects($this->once())
            ->method('evaluate')
            ->willReturn(EvaluationResult::pass(['meta1' => 'value1']));

        $handler2->expects($this->once())
            ->method('evaluate')
            ->willReturn(EvaluationResult::pass(['meta2' => 'value2']));

        $this->mockLogger->expects($this->exactly(2))
            ->method('debug');

        $result = $this->service->evaluateConditions($conditions, $this->context);

        $this->assertTrue($result->isPassed());
        $this->assertEquals(['meta1' => 'value1', 'meta2' => 'value2'], $result->getMetadata());
    }

    public function test_evaluate_conditions_some_fail(): void
    {
        $condition1 = $this->createMock(ConditionInterface::class);
        $condition2 = $this->createMock(ConditionInterface::class);
        $conditions = [$condition1, $condition2];

        $condition1->expects($this->any())->method('getType')->willReturn('type1');
        $condition1->expects($this->any())->method('getId')->willReturn(1);
        $condition2->expects($this->any())->method('getType')->willReturn('type2');
        $condition2->expects($this->any())->method('getId')->willReturn(2);

        $handler1 = $this->createMock(ConditionHandlerInterface::class);
        $handler2 = $this->createMock(ConditionHandlerInterface::class);

        $this->mockHandlerFactory->expects($this->exactly(2))
            ->method('getHandler')
            ->willReturnMap([
                ['type1', $handler1],
                ['type2', $handler2],
            ]);

        $handler1->expects($this->once())
            ->method('evaluate')
            ->willReturn(EvaluationResult::pass(['meta1' => 'value1']));

        $handler2->expects($this->once())
            ->method('evaluate')
            ->willReturn(EvaluationResult::fail(['失败消息'], ['meta2' => 'value2']));

        $this->mockLogger->expects($this->exactly(2))
            ->method('debug');

        $result = $this->service->evaluateConditions($conditions, $this->context);

        $this->assertFalse($result->isPassed());
        $this->assertEquals(['失败消息'], $result->getMessages());
        $this->assertEquals(['meta1' => 'value1', 'meta2' => 'value2'], $result->getMetadata());
    }

    public function test_get_condition_display_text(): void
    {
        $conditionType = 'test_type';
        $expectedText = '测试条件显示文本';

        $this->mockCondition->expects($this->once())
            ->method('getType')
            ->willReturn($conditionType);

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($conditionType)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('getDisplayText')
            ->with($this->mockCondition)
            ->willReturn($expectedText);

        $result = $this->service->getConditionDisplayText($this->mockCondition);

        $this->assertEquals($expectedText, $result);
    }

    public function test_get_available_condition_types_without_filter(): void
    {
        $expectedTypes = [
            'type1' => ['type' => 'type1', 'supportedTriggers' => ['before_action']],
            'type2' => ['type' => 'type2', 'supportedTriggers' => ['after_action']],
        ];

        $this->mockHandlerFactory->expects($this->once())
            ->method('getAvailableTypes')
            ->willReturn($expectedTypes);

        $result = $this->service->getAvailableConditionTypes();

        $this->assertEquals($expectedTypes, $result);
    }

    public function test_get_available_condition_types_with_trigger_filter(): void
    {
        $allTypes = [
            'type1' => ['type' => 'type1', 'supportedTriggers' => [ConditionTrigger::BEFORE_ACTION]],
            'type2' => ['type' => 'type2', 'supportedTriggers' => [ConditionTrigger::AFTER_ACTION]],
            'type3' => ['type' => 'type3', 'supportedTriggers' => [ConditionTrigger::BEFORE_ACTION, ConditionTrigger::VALIDATION]],
        ];

        $this->mockHandlerFactory->expects($this->once())
            ->method('getAvailableTypes')
            ->willReturn($allTypes);

        $result = $this->service->getAvailableConditionTypes(ConditionTrigger::BEFORE_ACTION);

        $expectedFiltered = [
            'type1' => ['type' => 'type1', 'supportedTriggers' => [ConditionTrigger::BEFORE_ACTION]],
            'type3' => ['type' => 'type3', 'supportedTriggers' => [ConditionTrigger::BEFORE_ACTION, ConditionTrigger::VALIDATION]],
        ];

        $this->assertEquals($expectedFiltered, $result);
    }

    public function test_validate_condition_config(): void
    {
        $type = 'test_type';
        $config = ['key' => 'value'];
        $expectedResult = ValidationResult::success();

        $this->mockHandlerFactory->expects($this->once())
            ->method('getHandler')
            ->with($type)
            ->willReturn($this->mockHandler);

        $this->mockHandler->expects($this->once())
            ->method('validateConfig')
            ->with($config)
            ->willReturn($expectedResult);

        $result = $this->service->validateConditionConfig($type, $config);

        $this->assertSame($expectedResult, $result);
    }
} 