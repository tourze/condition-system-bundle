<?php

declare(strict_types=1);

namespace Tourze\ConditionSystemBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Knp\Menu\ItemInterface;
use Tourze\ConditionSystemBundle\Entity\TestCondition;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 条件系统管理菜单配置
 */
class AdminMenu implements MenuProviderInterface
{
    /**
     * 获取条件系统管理菜单项
     *
     * @return array<mixed>
     */
    public static function getMenuItems(): array
    {
        return [
            MenuItem::section('条件系统管理', 'fa fa-cogs'),
            MenuItem::linkToCrud('测试条件管理', 'fa fa-check-circle', TestCondition::class),
        ];
    }

    public function __invoke(ItemInterface $item): void
    {
        // 这里可以根据需要添加菜单项
        // 目前这个类主要是提供静态菜单项，所以这个方法暂时为空
    }
}
