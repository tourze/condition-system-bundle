<?php

namespace Tourze\ConditionSystemBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tourze\ConditionSystemBundle\Exception\NotImplementedException;

class NotImplementedExceptionTest extends TestCase
{
    public function test_exception_extends_runtime_exception(): void
    {
        $exception = new NotImplementedException('Test message');
        
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }
    
    public function test_exception_message(): void
    {
        $message = 'Feature not implemented';
        $exception = new NotImplementedException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }
    
    public function test_exception_code(): void
    {
        $code = 501;
        $exception = new NotImplementedException('Test', $code);
        
        $this->assertEquals($code, $exception->getCode());
    }
    
    public function test_exception_previous(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new NotImplementedException('Test', 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
}