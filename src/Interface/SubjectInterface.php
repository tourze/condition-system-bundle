<?php

namespace Tourze\ConditionSystemBundle\Interface;

/**
 * 条件主体接口
 * 
 * 代表条件所作用的对象，如优惠券、活动、权限等
 */
interface SubjectInterface
{
    /**
     * 获取主体ID
     */
    public function getSubjectId(): string;

    /**
     * 获取主体类型
     */
    public function getSubjectType(): string;

    /**
     * 获取主体数据
     */
    public function getSubjectData(): array;
} 