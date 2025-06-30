<?php

namespace Tourze\ConditionSystemBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;
use Tourze\ConditionSystemBundle\Repository\BaseConditionRepository;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 基础条件实体
 */
#[ORM\Entity(repositoryClass: BaseConditionRepository::class)]
#[ORM\InheritanceType(value: 'JOINED')]
#[ORM\DiscriminatorColumn(name: 'condition_type', type: 'string')]
#[ORM\Table(name: 'condition_base', options: ['comment' => '条件基础表'])]
abstract class BaseCondition implements ConditionInterface, \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '条件类型'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '条件标签'])]
    private string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true, 'comment' => '是否启用'])]
    private bool $enabled = true;

    /**
     * 获取触发器类型 - 由子类实现
     */
    abstract public function getTrigger(): ConditionTrigger;

    /**
     * 转换为数组 - 由子类实现
     */
    abstract public function toArray(): array;

    /**
     * 获取主体 - 由子类实现
     */
    abstract public function getSubject(): ?SubjectInterface;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function __toString(): string
    {
        return $this->label ?? $this->type;
    }
}
