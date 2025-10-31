<?php

namespace Tourze\ConditionSystemBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Tourze\ConditionSystemBundle\Exception\ConditionHandlerNotFoundException;
use Tourze\ConditionSystemBundle\Interface\ConditionHandlerInterface;

/**
 * 条件处理器工厂
 */
#[Autoconfigure(public: true)]
class ConditionHandlerFactory
{
    /**
     * @var array<string, ConditionHandlerInterface>
     */
    private array $handlers = [];

    /**
     * @param iterable<ConditionHandlerInterface> $handlers
     */
    public function __construct(#[AutowireIterator(tag: 'condition_system.handler')] iterable $handlers)
    {
        foreach ($handlers as $handler) {
            if ($handler instanceof ConditionHandlerInterface) {
                $this->handlers[$handler->getType()] = $handler;
            }
        }
    }

    /**
     * 获取指定类型的处理器
     */
    public function getHandler(string $type): ConditionHandlerInterface
    {
        if (!isset($this->handlers[$type])) {
            throw new ConditionHandlerNotFoundException("未找到条件处理器: {$type}");
        }

        return $this->handlers[$type];
    }

    /**
     * 检查是否存在指定类型的处理器
     */
    public function hasHandler(string $type): bool
    {
        return isset($this->handlers[$type]);
    }

    /**
     * 获取所有处理器
     *
     * @return array<string, ConditionHandlerInterface>
     */
    public function getAllHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * 获取可用的条件类型
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableTypes(): array
    {
        $types = [];
        foreach ($this->handlers as $type => $handler) {
            $types[$type] = [
                'type' => $type,
                'label' => $handler->getLabel(),
                'description' => $handler->getDescription(),
                'supportedTriggers' => $handler->getSupportedTriggers(),
            ];
        }

        return $types;
    }
}
