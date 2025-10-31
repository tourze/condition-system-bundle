<?php

namespace Tourze\ConditionSystemBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ConditionSystemBundle\Exception\ConditionSystemException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ConditionSystemException::class)]
final class ConditionSystemExceptionTest extends AbstractExceptionTestCase
{
    /**
     * 创建一个具体的异常实现类用于测试
     */
    private function createExceptionInstance(?string $message = null, ?int $code = null, ?\Throwable $previous = null): ConditionSystemException
    {
        return new class($message, $code, $previous) extends ConditionSystemException {
            public function __construct(?string $message = null, ?int $code = null, ?\Throwable $previous = null)
            {
                parent::__construct($message ?? '测试异常', $code ?? 0, $previous);
            }
        };
    }

    public function testExceptionExtendsBaseException(): void
    {
        $exception = $this->createExceptionInstance();

        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = '条件系统发生错误';
        $exception = $this->createExceptionInstance($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = '条件系统错误';
        $code = 1001;
        $exception = $this->createExceptionInstance($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \RuntimeException('原始错误');
        $exception = $this->createExceptionInstance('条件系统错误', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testExceptionCanBeThrownAndCaught(): void
    {
        $this->expectException(ConditionSystemException::class);
        $this->expectExceptionMessage('测试异常');

        throw $this->createExceptionInstance('测试异常');
    }

    public function testExceptionInheritanceChain(): void
    {
        $exception = $this->createExceptionInstance();

        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(ConditionSystemException::class, $exception);
    }

    public function testExceptionDefaultValues(): void
    {
        $exception = $this->createExceptionInstance();

        $this->assertEquals('测试异常', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testExceptionStackTrace(): void
    {
        $exception = $this->createExceptionInstance('测试');

        $this->assertNotEmpty($exception->getTrace());
        $this->assertNotEmpty($exception->getTraceAsString());
        $this->assertStringContainsString(__CLASS__, $exception->getTraceAsString());
    }
}
