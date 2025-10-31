<?php

namespace Tourze\ConditionSystemBundle\Interface;

use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;

/**
 * 通用条件接口
 */
interface ConditionInterface
{
    /**
     * 获取条件ID
     */
    public function getId(): ?int;

    /**
     * 获取条件主体
     */
    public function getSubject(): ?SubjectInterface;

    /**
     * 获取条件类型
     */
    public function getType(): string;

    /**
     * 获取条件标签
     */
    public function getLabel(): string;

    /**
     * 是否启用
     */
    public function isEnabled(): bool;

    /**
     * 获取触发器类型
     */
    public function getTrigger(): ConditionTrigger;

    /**
     * 转换为数组
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
