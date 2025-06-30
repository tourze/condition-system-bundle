<?php

namespace Tourze\ConditionSystemBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ConditionSystemBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ConditionSystemBundleTest extends TestCase
{
    private ConditionSystemBundle $bundle;
    
    protected function setUp(): void
    {
        $this->bundle = new ConditionSystemBundle();
    }
    
    public function test_bundle_extends_symfony_bundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }
    
    public function test_bundle_name(): void
    {
        $this->assertEquals('ConditionSystemBundle', $this->bundle->getName());
    }
}