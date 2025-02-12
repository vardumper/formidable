<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest;

use vardumper\Formidable\Data;
use vardumper\Formidable\Exception\InvalidDataException;
use vardumper\Formidable\Exception\UnboundDataException;
use vardumper\Formidable\Form;
use vardumper\Formidable\FormError\FormError;
use vardumper\Formidable\Mapping\BindResult;
use vardumper\Formidable\Mapping\MappingInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers vardumper\Formidable\Form
 */
class FormTest extends TestCase
{
    public function testWithDefaults()
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind()->shouldNotBeCalled();

        $form = (new Form($mapping->reveal()))->withDefaults($data);

        $this->assertFalse($form->hasErrors());
        $this->assertSame('bar', $form->getField('foo')->getValue());
    }

    public function testBindValidData()
    {
        $data = Data::none();

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind($data)->willReturn(BindResult::fromValue('foo'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bind($data);

        $this->assertFalse($form->hasErrors());
        $this->assertSame('foo', $form->getValue());
    }

    public function testFill()
    {
        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->unbind(['foo' => 'bar'])->willReturn(Data::fromFlatArray(['foo' => 'bar']))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->fill(['foo' => 'bar']);
        $this->assertSame('bar', $form->getField('foo')->getValue());
    }

    public function testBindInvalidData()
    {
        $data = Data::none();

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind($data)->willReturn(BindResult::fromFormErrors(new FormError('', 'foo')))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bind($data);

        $this->assertTrue($form->hasErrors());
        $this->assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->getMessage());
        $this->expectException(InvalidDataException::class);
        $form->getValue();
    }

    public function testExceptionOnGetValueWithoutBoundData()
    {
        $form = new Form($this->prophesize(MappingInterface::class)->reveal());
        $this->expectException(UnboundDataException::class);
        $form->getValue();
    }

    public function testBindFromPostRequest()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('POST');
        $request->getParsedBody()->willReturn(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        $this->assertFalse($form->hasErrors());
        $this->assertSame('bar', $form->getValue());
    }

    public function specialMethodProvider() : array
    {
        return [
            ['PATCH'],
            ['PUT'],
        ];
    }

    /**
     * @dataProvider specialMethodProvider
     */
    public function testBindFromPatchRequest(string $method)
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->__toString()->willReturn('foo=bar');

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn($method);
        $request->getBody()->willReturn($stream->reveal());

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        $this->assertFalse($form->hasErrors());
        $this->assertSame('bar', $form->getValue());
    }

    public function testBindFromGetRequest()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        $this->assertFalse($form->hasErrors());
        $this->assertSame('bar', $form->getValue());
    }

    public function testBindFromRequestTrimsByDefault()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => ' bar ']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        (new Form($mapping->reveal()))->bindFromRequest($request->reveal());
    }

    public function testTrimForBindFromRequestCanBeDisabled()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => ' bar ']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && ' bar ' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue(' bar '))->shouldBeCalled();

        (new Form($mapping->reveal()))->bindFromRequest($request->reveal(), false);
    }

    public function testWithError()
    {
        $form = (
            new Form($this->prophesize(MappingInterface::class)->reveal())
        )->withError(new FormError('bar', 'foo'));
        $this->assertTrue($form->hasErrors());
        $this->assertSame('bar', iterator_to_array($form->getErrors())[0]->getKey());
        $this->assertSame('foo', iterator_to_array($form->getErrors())[0]->getMessage());
    }

    public function testWithGlobalError()
    {
        $form = (new Form($this->prophesize(MappingInterface::class)->reveal()))->withGlobalError('foo');
        $this->assertTrue($form->hasErrors());
        $this->assertTrue($form->hasGlobalErrors());
        $this->assertSame('', iterator_to_array($form->getGlobalErrors())[0]->getKey());
        $this->assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->getMessage());
    }

    public function testFieldRetrivalFromUnknownField()
    {
        $form = new Form($this->prophesize(MappingInterface::class)->reveal());
        $field = $form->getField('foo');

        $this->assertSame('foo', $field->getKey());
        $this->assertSame('', $field->getValue());
        $this->assertTrue($field->getErrors()->isEmpty());
    }
}
