<?php

namespace Tourze\ConditionSystemBundle\Interface;

/**
 * 条件执行者接口
 *
 * 代表执行条件检查的对象，如用户、系统、设备等
 */
interface ActorInterface
{
    /**
     * 获取执行者ID
     */
    public function getActorId(): string;

    /**
     * 获取执行者类型
     */
    public function getActorType(): string;

    /**
     * 获取执行者数据
     */
    public function getActorData(): array;
} 