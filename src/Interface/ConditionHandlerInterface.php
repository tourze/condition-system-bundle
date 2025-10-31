<?php

namespace Tourze\ConditionSystemBundle\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;
use Tourze\ConditionSystemBundle\ValueObject\FormField;
use Tourze\ConditionSystemBundle\ValueObject\ValidationResult;

/**
 * 通用条件处理器接口
 */
#[AutoconfigureTag(name: 'condition_system.handler')]
interface ConditionHandlerInterface
{
    /**
     * 获取条件类型标识符
     */
    public function getType(): string;

    /**
     * 获取条件类型显示名称
     */
    public function getLabel(): string;

    /**
     * 获取条件描述
     */
    public function getDescription(): string;

    /**
     * 获取表单字段配置
     *
     * @return iterable<FormField>
     */
    public function getFormFields(): iterable;

    /**
     * 验证条件配置的有效性
     *
     * @param array<string, mixed> $config
     */
    public function validateConfig(array $config): ValidationResult;

    /**
     * 创建条件实体
     *
     * @param array<string, mixed> $config
     */
    public function createCondition(SubjectInterface $subject, array $config): ConditionInterface;

    /**
     * 更新条件实体
     *
     * @param array<string, mixed> $config
     */
    public function updateCondition(ConditionInterface $condition, array $config): void;

    /**
     * 评估条件是否满足
     */
    public function evaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult;

    /**
     * 获取条件的显示文本
     */
    public function getDisplayText(ConditionInterface $condition): string;

    /**
     * 获取支持的触发器类型
     *
     * @return array<int, ConditionTrigger>
     */
    public function getSupportedTriggers(): array;
}
