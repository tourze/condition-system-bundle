<?php

namespace Tourze\ConditionSystemBundle\Handler;

use Tourze\ConditionSystemBundle\Interface\ConditionHandlerInterface;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;

/**
 * 抽象条件处理器
 */
abstract class AbstractConditionHandler implements ConditionHandlerInterface
{
    /**
     * 评估条件
     */
    public function evaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult
    {
        if (!$condition->isEnabled()) {
            return EvaluationResult::pass(['reason' => 'condition_disabled']);
        }

        return $this->doEvaluate($condition, $context);
    }

    /**
     * 执行具体的评估逻辑 - 由子类实现
     */
    abstract protected function doEvaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult;
}
