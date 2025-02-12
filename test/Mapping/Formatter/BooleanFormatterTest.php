<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Formatter;

use vardumper\Formidable\Data;
use vardumper\Formidable\Mapping\Formatter\BooleanFormatter;
use vardumper\Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\Formatter\BooleanFormatter
 */
class BooleanFormatterTest extends TestCase
{
    public function testBindValidTrueValue()
    {
        $this->assertTrue((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'true']))->getValue());
    }

    public function testBindValidFalseValue()
    {
        $this->assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => 'false']))->getValue());
    }

    public function testFallbackToFalseOnBindNonExistentKey()
    {
        $this->assertFalse((new BooleanFormatter())->bind('foo', Data::fromFlatArray([]))->getValue());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new BooleanFormatter())->bind('foo', Data::fromFlatArray(['foo' => '']));
        $this->assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        $this->assertSame('foo', $error->getKey());
        $this->assertSame('error.boolean', $error->getMessage());
    }

    public function testUnbindValidTrueValue()
    {
        $data = (new BooleanFormatter())->unbind('foo', true);
        $this->assertSame('true', $data->getValue('foo'));
    }

    public function testUnbindValidFalseValue()
    {
        $data = (new BooleanFormatter())->unbind('foo', false);
        $this->assertSame('false', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new BooleanFormatter())->unbind('foo', 'false');
    }
}
