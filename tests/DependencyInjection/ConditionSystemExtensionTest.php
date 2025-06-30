<?php

namespace Tourze\ConditionSystemBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\ConditionSystemBundle\DependencyInjection\ConditionSystemExtension;
use Tourze\ConditionSystemBundle\Service\ConditionManagerService;
use Tourze\ConditionSystemBundle\Service\ConditionHandlerFactory;

class ConditionSystemExtensionTest extends TestCase
{
    private ConditionSystemExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new ConditionSystemExtension();
        $this->container = new ContainerBuilder();
    }

    public function test_load_registers_services(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证核心服务已注册
        $this->assertTrue($this->container->hasDefinition(ConditionManagerService::class));
        $this->assertTrue($this->container->hasDefinition(ConditionHandlerFactory::class));
    }

    public function test_services_are_autoconfigured(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证自动配置标签
        $definition = $this->container->getDefinition(ConditionManagerService::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
    }

    public function test_handler_tag_is_registered(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证标签已注册
        $this->assertTrue($this->container->hasParameter('condition_system.handler_tag'));
        $this->assertEquals('condition_system.handler', $this->container->getParameter('condition_system.handler_tag'));
    }
}