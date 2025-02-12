<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Helper;

use vardumper\Formidable\Data;
use vardumper\Formidable\Field;
use vardumper\Formidable\FormError\FormErrorSequence;
use vardumper\Formidable\Helper\Exception\InvalidSelectLabelException;
use vardumper\Formidable\Helper\Select;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Helper\Select
 * @covers vardumper\Formidable\Helper\AttributeTrait
 */
class SelectTest extends TestCase
{
    public function testDefaultSelect()
    {
        $helper = new Select();
        $this->assertSame(
            '<select id="input.foo" name="foo"><option value="foo">bar</option></select>',
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar']
            )
        );
    }

    public function testIntegerKeys()
    {
        $helper = new Select();
        $this->assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"1\">bar</option>\n"
            . "<option value=\"2\">baz</option></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['1' => 'bar', 2 => 'baz']
            )
        );
    }

    public function testSingleSelectedValue()
    {
        $helper = new Select();
        $this->assertSame(
            '<select id="input.foo" name="foo"><option value="foo" selected>bar</option></select>',
            $helper(
                new Field('foo', 'foo', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar']
            )
        );
    }

    public function testMultipleSelectedValues()
    {
        $helper = new Select();
        $this->assertSame(
            "<select multiple id=\"input.foo\" name=\"foo[]\"><option value=\"foo\" selected>bar</option>\n"
            . "<option value=\"baz\" selected>bat</option>\n"
            . "<option value=\"a\">b</option></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::fromNestedArray([
                    'foo' => [
                        'foo',
                        'baz',
                    ],
                ])),
                ['foo' => 'bar', 'baz' => 'bat', 'a' => 'b'],
                ['multiple' => 'multiple']
            )
        );
    }

    public function testOptGroups()
    {
        $helper = new Select();
        $this->assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"foo\">bar</option>\n"
            . "<optgroup label=\"baz\"><option value=\"bat\">a</option></optgroup></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar', 'baz' => ['bat' => 'a']]
            )
        );
    }

    public function testExceptionOnInvalidLabel()
    {
        $helper = new Select();
        $this->expectException(InvalidSelectLabelException::class);
        $helper(
            new Field('foo', '', new FormErrorSequence(), Data::none()),
            ['foo' => true]
        );
    }
}
