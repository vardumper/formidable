<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Exception;

use vardumper\Formidable\Exception\InvalidDataException;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Exception\InvalidDataException
 */
class InvalidDataExceptionTest extends TestCase
{
    public function testFromGetValueAttempt()
    {
        $this->assertSame(
            'Value cannot be retrieved when the form has errors',
            InvalidDataException::fromGetValueAttempt()->getMessage()
        );
    }
}
