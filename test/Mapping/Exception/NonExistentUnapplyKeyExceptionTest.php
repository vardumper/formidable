<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Exception;

use vardumper\Formidable\Mapping\Exception\NonExistentUnapplyKeyException;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\Exception\NonExistentUnapplyKeyException
 */
class NonExistentUnapplyKeyExceptionTest extends TestCase
{
    public function testFromNonExistentUnapplyKey()
    {
        $this->assertSame(
            'Key "foo" not found in array returned by unapply function',
            NonExistentUnapplyKeyException::fromNonExistentUnapplyKey('foo')->getMessage()
        );
    }
}
