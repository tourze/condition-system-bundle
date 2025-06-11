<?php

namespace Tourze\ConditionSystemBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Exception\InvalidConditionConfigException;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

/**
 * 条件管理服务
 */
class ConditionManagerService
{
    public function __construct(
        private readonly ConditionHandlerFactory $handlerFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * 创建条件
     */
    public function createCondition(SubjectInterface $subject, string $type, array $config): ConditionInterface
    {
        $handler = $this->handlerFactory->getHandler($type);
        
        $validation = $handler->validateConfig($config);
        if (!$validation->isValid()) {
            throw new InvalidConditionConfigException(implode('; ', $validation->getErrors()));
        }

        $condition = $handler->createCondition($subject, $config);
        
        $this->entityManager->persist($condition);
        $this->entityManager->flush();

        $this->logger->info('条件创建成功', [
            'type' => $type,
            'subject_id' => $subject->getSubjectId(),
            'subject_type' => $subject->getSubjectType(),
            'condition_id' => $condition->getId(),
        ]);

        return $condition;
    }

    /**
     * 更新条件
     */
    public function updateCondition(ConditionInterface $condition, array $config): void
    {
        $handler = $this->handlerFactory->getHandler($condition->getType());
        
        $validation = $handler->validateConfig($config);
        if (!$validation->isValid()) {
            throw new InvalidConditionConfigException(implode('; ', $validation->getErrors()));
        }

        $handler->updateCondition($condition, $config);
        
        $this->entityManager->persist($condition);
        $this->entityManager->flush();

        $this->logger->info('条件更新成功', [
            'condition_id' => $condition->getId(),
            'type' => $condition->getType(),
        ]);
    }

    /**
     * 删除条件
     */
    public function deleteCondition(ConditionInterface $condition): void
    {
        $this->entityManager->remove($condition);
        $this->entityManager->flush();

        $this->logger->info('条件删除成功', [
            'condition_id' => $condition->getId(),
            'type' => $condition->getType(),
        ]);
    }

    /**
     * 评估条件
     */
    public function evaluateCondition(ConditionInterface $condition, EvaluationContext $context): EvaluationResult
    {
        $handler = $this->handlerFactory->getHandler($condition->getType());
        
        try {
            $result = $handler->evaluate($condition, $context);
            
            $this->logger->debug('条件评估完成', [
                'condition_id' => $condition->getId(),
                'type' => $condition->getType(),
                'passed' => $result->isPassed(),
                'actor_id' => $context->getActor()->getActorId(),
            ]);
            
            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('条件评估失败', [
                'condition_id' => $condition->getId(),
                'type' => $condition->getType(),
                'error' => $e->getMessage(),
                'actor_id' => $context->getActor()->getActorId(),
            ]);
            
            return EvaluationResult::fail(['系统错误，请稍后重试']);
        }
    }

    /**
     * 批量评估条件
     */
    public function evaluateConditions(array $conditions, EvaluationContext $context): EvaluationResult
    {
        $allPassed = true;
        $allMessages = [];
        $allMetadata = [];

        foreach ($conditions as $condition) {
            if (!$condition instanceof ConditionInterface) {
                continue;
            }

            $result = $this->evaluateCondition($condition, $context);
            
            if (!$result->isPassed()) {
                $allPassed = false;
                $allMessages = array_merge($allMessages, $result->getMessages());
            }
            
            $allMetadata = array_merge($allMetadata, $result->getMetadata());
        }

        return $allPassed 
            ? EvaluationResult::pass($allMetadata)
            : EvaluationResult::fail($allMessages, $allMetadata);
    }

    /**
     * 获取条件显示文本
     */
    public function getConditionDisplayText(ConditionInterface $condition): string
    {
        $handler = $this->handlerFactory->getHandler($condition->getType());
        return $handler->getDisplayText($condition);
    }

    /**
     * 获取可用的条件类型
     */
    public function getAvailableConditionTypes(?ConditionTrigger $trigger = null): array
    {
        $types = $this->handlerFactory->getAvailableTypes();
        
        if ($trigger === null) {
            return $types;
        }

        // 过滤支持指定触发器的类型
        return array_filter($types, function ($typeInfo) use ($trigger) {
            return in_array($trigger, $typeInfo['supportedTriggers'], true);
        });
    }

    /**
     * 验证条件配置
     */
    public function validateConditionConfig(string $type, array $config): ValidationResult
    {
        $handler = $this->handlerFactory->getHandler($type);
        return $handler->validateConfig($config);
    }
} 