<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Exception;

use vardumper\Formidable\Mapping\Exception\UnbindFailureException;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\Exception\UnbindFailureException
 * @covers vardumper\Formidable\Mapping\Exception\NestedMappingExceptionTrait
 */
class UnbindFailureExceptionTest extends TestCase
{
    public function testFromUnbindExceptionWithGenericException()
    {
        $previous = new Exception('test');
        $exception = UnbindFailureException::fromUnbindException('foo', $previous);

        $this->assertSame(
            'Failed to unbind foo: test',
            $exception->getMessage()
        );
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testFromUnbindExceptionWithNestedBindFailureException()
    {
        $previous = UnbindFailureException::fromUnbindException(
            'bar',
            UnbindFailureException::fromUnbindException('baz', new Exception('test'))
        );
        $exception = UnbindFailureException::fromUnbindException('foo', $previous);

        $this->assertSame(
            'Failed to unbind foo.bar.baz: test',
            $exception->getMessage()
        );
        $this->assertSame($previous, $exception->getPrevious());
    }
}
