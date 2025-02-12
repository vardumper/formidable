<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Exception;

use vardumper\Formidable\Exception\InvalidKeyException;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Exception\InvalidKeyException
 */
class InvalidKeyExceptionTest extends TestCase
{
    public function testFromArrayWithNonStringKeys()
    {
        $this->assertSame(
            'Non-string key in array found',
            InvalidKeyException::fromArrayWithNonStringKeys([])->getMessage()
        );
    }

    public function testFromNonNestedKey()
    {
        $this->assertSame(
            'Expected string or nested integer key, but "boolean" was provided',
            InvalidKeyException::fromNonNestedKey(true)->getMessage()
        );
    }
}
