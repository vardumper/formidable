<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Constraint\Exception;

use vardumper\Formidable\Mapping\Constraint\Exception\InvalidStepException;
use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers vardumper\Formidable\Mapping\Constraint\Exception\InvalidStepException
 */
class InvalidStepExceptionTest extends TestCase
{
    public function testFromNonNumericStepWithString()
    {
        $this->assertSame(
            'Step was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericStep('test')->getMessage()
        );
    }

    public function testFromNonNumericStepWithObject()
    {
        $this->assertSame(
            'Step was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericStep(new stdClass())->getMessage()
        );
    }

    public function testFromNonNumericBaseWithString()
    {
        $this->assertSame(
            'Base was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericBase('test')->getMessage()
        );
    }

    public function testFromNonNumericBaseWithObject()
    {
        $this->assertSame(
            'Base was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericBase(new stdClass())->getMessage()
        );
    }

    public function testFromZeroOrNegativeStep()
    {
        $this->assertSame(
            'Step must be greater than zero, but got 0',
            InvalidStepException::fromZeroOrNegativeStep(Decimal::fromInteger(0))->getMessage()
        );
    }
}
