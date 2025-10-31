<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Exception\NotImplementedException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(NotImplementedException::class)]
final class NotImplementedExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionExtendsRuntimeException(): void
    {
        $exception = new NotImplementedException('Test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionMessage(): void
    {
        $message = 'Feature not implemented';
        $exception = new NotImplementedException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $code = 501;
        $exception = new NotImplementedException('Test', $code);

        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new NotImplementedException('Test', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
