<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Helper\Exception;

use vardumper\Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Helper\Exception\InvalidHtmlAttributeKeyException
 */
class InvalidHtmlAttributeKeyExceptionTest extends TestCase
{
    public function testFromInvalidKey()
    {
        $this->assertSame(
            'HTML attribute key must be of type string, but got integer',
            InvalidHtmlAttributeKeyException::fromInvalidKey(1)->getMessage()
        );
    }
}
