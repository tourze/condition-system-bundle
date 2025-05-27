<?php

namespace Tourze\ConditionSystemBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 通用条件触发器枚举
 */
enum ConditionTrigger: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case BEFORE_ACTION = 'before_action';    // 动作前置条件
    case AFTER_ACTION = 'after_action';      // 动作后置条件  
    case DURING_ACTION = 'during_action';    // 动作中条件
    case VALIDATION = 'validation';          // 验证条件
    case FILTER = 'filter';                  // 过滤条件

    public function getLabel(): string
    {
        return match ($this) {
            self::BEFORE_ACTION => '前置条件',
            self::AFTER_ACTION => '后置条件',
            self::DURING_ACTION => '执行中条件',
            self::VALIDATION => '验证条件',
            self::FILTER => '过滤条件',
        };
    }
}
