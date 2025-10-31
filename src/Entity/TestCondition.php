<?php

namespace Tourze\ConditionSystemBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\ConditionSystemBundle\Interface\SubjectInterface;

/**
 * 测试用条件实体
 */
#[ORM\Entity]
#[ORM\Table(name: 'condition_test', options: ['comment' => '测试条件表'])]
class TestCondition extends BaseCondition
{
    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '触发器类型'])]
    #[Assert\Length(max: 50)]
    #[Assert\NotBlank]
    private string $triggerType;

    public function __construct()
    {
        $this->triggerType = ConditionTrigger::BEFORE_ACTION->value;
    }

    public function getTrigger(): ConditionTrigger
    {
        return ConditionTrigger::from($this->triggerType);
    }

    public function setTriggerType(ConditionTrigger $trigger): void
    {
        $this->triggerType = $trigger->value;
    }

    public function getTriggerType(): string
    {
        return $this->triggerType;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
            'trigger' => $this->getTrigger()->value,
            'enabled' => $this->isEnabled(),
        ];
    }

    public function getSubject(): ?SubjectInterface
    {
        return null;
    }
}
