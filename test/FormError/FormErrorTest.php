<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\FormError;

use vardumper\Formidable\FormError\FormError;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\FormError\FormError
 */
class FormErrorTest extends TestCase
{
    public function testKeyRetrieval()
    {
        $this->assertSame('foo', (new FormError('foo', ''))->getKey());
    }

    public function testMessageRetrieval()
    {
        $this->assertSame('foo', (new FormError('', 'foo'))->getMessage());
    }

    public function testArgumentsRetrieval()
    {
        $this->assertSame(['foo' => 'bar'], (new FormError('', '', ['foo' => 'bar']))->getArguments());
    }
}
