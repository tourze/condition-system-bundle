<?php

namespace Tourze\ConditionSystemBundle\ValueObject;

/**
 * 验证结果值对象
 */
class ValidationResult
{
    /**
     * @param array<string> $errors
     */
    private function __construct(
        private readonly bool $valid,
        private readonly array $errors = [],
    ) {
    }

    /**
     * 创建成功结果
     */
    public static function success(): self
    {
        return new self(true);
    }

    /**
     * 创建失败结果
     *
     * @param array<string> $errors
     */
    public static function failure(array $errors): self
    {
        return new self(false, $errors);
    }

    /**
     * 是否验证通过
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * 获取错误信息
     *
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 获取第一个错误信息
     */
    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }
}
