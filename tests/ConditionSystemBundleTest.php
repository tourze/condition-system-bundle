<?php

declare(strict_types=1);

namespace Tourze\ConditionSystemBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\ConditionSystemBundle\ConditionSystemBundle;
use Tourze\ConditionSystemBundle\DependencyInjection\ConditionSystemExtension;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(ConditionSystemBundle::class)]
#[RunTestsInSeparateProcesses]
final class ConditionSystemBundleTest extends AbstractBundleTestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundleClass = self::getBundleClass();
        $bundle = new $bundleClass();
        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertSame('ConditionSystemBundle', $bundle->getName());
    }

    public function testBundleHasDependencies(): void
    {
        $dependencies = ConditionSystemBundle::getBundleDependencies();
        $this->assertArrayHasKey(DoctrineBundle::class, $dependencies);
        $this->assertSame(['all' => true], $dependencies[DoctrineBundle::class]);
    }

    public function testBundleHasExtension(): void
    {
        $bundleClass = self::getBundleClass();
        /** @var ConditionSystemBundle $bundle */
        $bundle = new $bundleClass();
        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(ConditionSystemExtension::class, $extension);
        $this->assertSame('condition_system', $extension->getAlias());
    }
}
