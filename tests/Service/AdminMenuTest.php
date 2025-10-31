<?php

namespace Tourze\ConditionSystemBundle\Tests\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\ConditionSystemBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 空实现，满足抽象方法要求
    }

    public function testGetMenuItems(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        $this->assertIsArray($menuItems);
        $this->assertNotEmpty($menuItems);

        // 验证菜单项结构 - 检查具体的菜单项类型
        $this->assertInstanceOf(SectionMenuItem::class, $menuItems[0]);
        $this->assertInstanceOf(CrudMenuItem::class, $menuItems[1]);
    }
}
