<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Formatter;

use vardumper\Formidable\Data;
use vardumper\Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use vardumper\Formidable\Mapping\Formatter\TextFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\Formatter\TextFormatter
 */
class TextFormatterTest extends TestCase
{
    public function testBindValidValue()
    {
        $this->assertSame('bar', (new TextFormatter())->bind(
            'foo',
            Data::fromFlatArray(['foo' => 'bar'])
        )->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        $this->assertTrue($bindResult->isSuccess());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new TextFormatter())->bind('foo', Data::fromFlatArray([]));
        $this->assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        $this->assertSame('foo', $error->getKey());
        $this->assertSame('error.required', $error->getMessage());
    }

    public function testUnbindValidValue()
    {
        $data = (new TextFormatter())->unbind('foo', 'bar');
        $this->assertSame('bar', $data->getValue('foo'));
    }

    public function testUnbindInvalidValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new TextFormatter())->unbind('foo', 1);
    }
}
