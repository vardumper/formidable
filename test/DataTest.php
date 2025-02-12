<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest;

use vardumper\Formidable\Data;
use vardumper\Formidable\Exception\InvalidKeyException;
use vardumper\Formidable\Exception\InvalidValueException;
use vardumper\Formidable\Exception\NonExistentKeyException;
use vardumper\Formidable\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Data
 */
class DataTest extends TestCase
{
    public function testGetValueReturnsSetValue()
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        $this->assertSame('bar', $data->getValue('foo'));
    }

    public function testGetValueReturnsFallbackWhenKeyDoesNotExist()
    {
        $data = Data::none();
        $this->assertSame('bar', $data->getValue('foo', 'bar'));
    }

    public function testGetValueThrowsExceptionWithoutFallback()
    {
        $this->expectException(NonExistentKeyException::class);
        $data = Data::none();
        $data->getValue('foo');
    }

    public function testHasKey()
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        $this->assertTrue($data->hasKey('foo'));
        $this->assertFalse($data->hasKey('bar'));
    }

    public function testMerge()
    {
        $data1 = Data::fromFlatArray(['foo' => 'bar']);
        $data2 = Data::fromFlatArray(['foo' => 'baz', 'baz' => 'bat']);
        $mergedData = $data1->merge($data2);

        $this->assertSame('bar', $mergedData->getValue('foo'));
        $this->assertSame('bat', $mergedData->getValue('baz'));
    }

    public function testFilter()
    {
        $data = Data::fromFlatArray(['foo' => 'bar', 'baz' => 'bat'])->filter(function (string $value, string $key) {
            return $key === 'baz';
        });

        $this->assertFalse($data->hasKey('foo'));
        $this->assertTrue($data->hasKey('baz'));
    }

    public function testTransform()
    {
        $transformer = $this->prophesize(TransformerInterface::class);
        $transformer->__invoke(' bar ', 'foo')->willReturn('bar');
        $transformer->__invoke(' bat ', 'baz')->willReturn(' bat');

        $data = Data::fromFlatArray([
            'foo' => ' bar ',
            'baz' => ' bat ',
        ])->transform($transformer->reveal());

        $this->assertSame('bar', $data->getValue('foo'));
        $this->assertSame(' bat', $data->getValue('baz'));
    }

    public function testCreateNoneData()
    {
        $data = Data::none();
        $this->assertAttributeSame([], 'data', $data);
    }

    public function testCreateFromFlatArrayWithInvalidKey()
    {
        $this->expectException(InvalidKeyException::class);
        Data::fromFlatArray([0 => 'foo']);
    }

    public function testCreateFromFlatArrayWithInvalidValue()
    {
        $this->expectException(InvalidValueException::class);
        Data::fromFlatArray(['foo' => 0]);
    }

    public function testCreateFromNestedArray()
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar' => ['baz', 'bat'],
            ]
        ]);

        $this->assertSame('baz', $data->getValue('foo[bar][0]'));
        $this->assertSame('bat', $data->getValue('foo[bar][1]'));
    }

    public function testCreateFromNestedArrayWithInvalidValue()
    {
        $this->expectException(InvalidValueException::class);
        Data::fromNestedArray(['foo' => 1]);
    }

    public function testCreateFromNestedArrayWithRootIntegerKey()
    {
        $this->expectException(InvalidKeyException::class);
        Data::fromNestedArray([0 => 'foo']);
    }

    public function testCreateFromNestedArrayWithChildIntegerKey()
    {
        $data = Data::fromNestedArray(['foo' => [0 => 'bar']]);
        $this->assertSame('bar', $data->getValue('foo[0]'));
    }

    public function testCreateFromNestedArrayWithRootStringKey()
    {
        $data = Data::fromNestedArray(['foo' => 'bar']);
        $this->assertSame('bar', $data->getValue('foo'));
    }

    public function testCreateFromNestedArrayWithChildStringKey()
    {
        $data = Data::fromNestedArray(['foo' => ['bar' => 'baz']]);
        $this->assertSame('baz', $data->getValue('foo[bar]'));
    }

    public function testGetIndexes()
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar',
                'baz' => 'bat',
                [
                    'foo',
                    'bar',
                ]
            ],
        ]);

        $this->assertSame(['0', 'baz', '1'], $data->getIndexes('foo'));
    }

    public function testIsEmptyReturnsTrueWithoutData()
    {
        $data = Data::none();
        $this->assertTrue($data->isEmpty());
    }

    public function testIsEmptyReturnsFalseWithData()
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        $this->assertFalse($data->isEmpty());
    }
}
