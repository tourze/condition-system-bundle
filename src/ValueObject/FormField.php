<?php

namespace Tourze\ConditionSystemBundle\ValueObject;

/**
 * 表单字段值对象
 */
class FormField
{
    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $constraints
     */
    private function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string $label,
        private readonly bool $required = false,
        private readonly ?string $help = null,
        private readonly array $options = [],
        private readonly array $constraints = [],
    ) {
    }

    public static function create(string $name, string $type, string $label): self
    {
        return new self($name, $type, $label);
    }

    public function required(bool $required = true): self
    {
        return new self(
            $this->name,
            $this->type,
            $this->label,
            $required,
            $this->help,
            $this->options,
            $this->constraints
        );
    }

    public function help(string $help): self
    {
        return new self(
            $this->name,
            $this->type,
            $this->label,
            $this->required,
            $help,
            $this->options,
            $this->constraints
        );
    }

    /**
     * @param array<string, mixed> $options
     */
    public function options(array $options): self
    {
        return new self(
            $this->name,
            $this->type,
            $this->label,
            $this->required,
            $this->help,
            array_merge($this->options, $options),
            $this->constraints
        );
    }

    /**
     * @param array<string, mixed> $constraints
     */
    public function constraints(array $constraints): self
    {
        return new self(
            $this->name,
            $this->type,
            $this->label,
            $this->required,
            $this->help,
            $this->options,
            array_merge($this->constraints, $constraints)
        );
    }

    public function min(int|float $min): self
    {
        return $this->constraints(['min' => $min]);
    }

    public function max(int|float $max): self
    {
        return $this->constraints(['max' => $max]);
    }

    /**
     * @param array<string, mixed> $choices
     */
    public function choices(array $choices): self
    {
        return $this->options(['choices' => $choices]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'required' => $this->required,
            'help' => $this->help,
            'options' => $this->options,
            'constraints' => $this->constraints,
        ];
    }
}
