<?php

namespace Tourze\ConditionSystemBundle\ValueObject;

/**
 * 条件评估结果
 */
class EvaluationResult
{
    private function __construct(
        private readonly bool $passed,
        private readonly array $messages = [],
        private readonly array $metadata = []
    ) {}

    /**
     * 创建通过结果
     */
    public static function pass(array $metadata = []): self
    {
        return new self(true, [], $metadata);
    }

    /**
     * 创建失败结果
     */
    public static function fail(array $messages, array $metadata = []): self
    {
        return new self(false, $messages, $metadata);
    }

    /**
     * 是否通过
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }

    /**
     * 获取消息
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * 获取第一个消息
     */
    public function getFirstMessage(): ?string
    {
        return $this->messages[0] ?? null;
    }

    /**
     * 获取元数据
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * 获取指定元数据值
     */
    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }
} 