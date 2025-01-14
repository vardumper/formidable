<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Constraint\Exception;

use vardumper\Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers vardumper\Formidable\Mapping\Constraint\Exception\InvalidTypeException
 */
class InvalidTypeExceptionTest extends TestCase
{
    public function testFromInvalidTypeWithObject()
    {
        $this->assertSame(
            'Value was expected to be of type foo, but got stdClass',
            InvalidTypeException::fromInvalidType(new stdClass(), 'foo')->getMessage()
        );
    }

    public function testFromInvalidTypeWithScalar()
    {
        $this->assertSame(
            'Value was expected to be of type foo, but got boolean',
            InvalidTypeException::fromInvalidType(true, 'foo')->getMessage()
        );
    }

    public function testFromNonNumericValueWithString()
    {
        $this->assertSame(
            'Value was expected to be numeric, but got "test"',
            InvalidTypeException::fromNonNumericValue('test')->getMessage()
        );
    }

    public function testFromNonNumericValueWithObject()
    {
        $this->assertSame(
            'Value was expected to be numeric, but got object',
            InvalidTypeException::fromNonNumericValue(new stdClass())->getMessage()
        );
    }
}
