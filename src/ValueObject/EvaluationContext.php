<?php

namespace Tourze\ConditionSystemBundle\ValueObject;

use Tourze\ConditionSystemBundle\Interface\ActorInterface;

/**
 * 条件评估上下文
 */
class EvaluationContext
{
    /**
     * @param array<string, mixed> $metadata
     */
    private function __construct(
        private readonly ActorInterface $actor,
        private readonly ?object $payload = null,
        private readonly array $metadata = [],
    ) {
    }

    /**
     * 创建评估上下文
     */
    public static function create(ActorInterface $actor): self
    {
        return new self($actor);
    }

    /**
     * 添加载荷数据
     */
    public function withPayload(object $payload): self
    {
        return new self($this->actor, $payload, $this->metadata);
    }

    /**
     * 添加元数据
     *
     * @param array<string, mixed> $metadata
     */
    public function withMetadata(array $metadata): self
    {
        return new self($this->actor, $this->payload, $metadata);
    }

    /**
     * 获取执行者
     */
    public function getActor(): ActorInterface
    {
        return $this->actor;
    }

    /**
     * 获取载荷数据
     */
    public function getPayload(): ?object
    {
        return $this->payload;
    }

    /**
     * 获取元数据
     *
     * @return array<string, mixed>
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
