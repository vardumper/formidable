<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping;

use vardumper\Formidable\Mapping\Constraint\EmailAddressConstraint;
use vardumper\Formidable\Mapping\Constraint\NotEmptyConstraint;
use vardumper\Formidable\Mapping\Constraint\UrlConstraint;
use vardumper\Formidable\Mapping\FieldMapping;
use vardumper\Formidable\Mapping\FieldMappingFactory;
use vardumper\Formidable\Mapping\Formatter\BooleanFormatter;
use vardumper\Formidable\Mapping\Formatter\DateFormatter;
use vardumper\Formidable\Mapping\Formatter\DateTimeFormatter;
use vardumper\Formidable\Mapping\Formatter\DecimalFormatter;
use vardumper\Formidable\Mapping\Formatter\FloatFormatter;
use vardumper\Formidable\Mapping\Formatter\IgnoredFormatter;
use vardumper\Formidable\Mapping\Formatter\IntegerFormatter;
use vardumper\Formidable\Mapping\Formatter\TextFormatter;
use vardumper\Formidable\Mapping\Formatter\TimeFormatter;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\FieldMappingFactory
 */
class FieldMappingFactoryTest extends TestCase
{
    public function testIgnoredFactory()
    {
        $fieldMapping = FieldMappingFactory::ignored('foo');
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(IgnoredFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testTextFactoryWithoutConstraints()
    {
        $fieldMapping = FieldMappingFactory::text();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testTextFactoryWithConstraints()
    {
        $fieldMapping = FieldMappingFactory::text(1, 2, 'iso-8859-15');
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(2, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[0]);
        $this->assertAttributeSame(1, 'lengthLimit', $constraints[0]);

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[1]);
        $this->assertAttributeSame(2, 'lengthLimit', $constraints[1]);
    }

    public function testNonEmptyTextFactoryWithoutConstraints()
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertInstanceOf(NotEmptyConstraint::class, $constraints[0]);
    }

    public function testNonEmptyTextFactoryWithConstraints()
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2, 'iso-8859-15');
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[0]);
        $this->assertAttributeSame(1, 'lengthLimit', $constraints[0]);

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[1]);
        $this->assertAttributeSame(2, 'lengthLimit', $constraints[1]);

        $this->assertInstanceOf(NotEmptyConstraint::class, $constraints[2]);
    }

    public function testEmailAddressFactory()
    {
        $fieldMapping = FieldMappingFactory::emailAddress();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);
        $this->assertInstanceOf(EmailAddressConstraint::class, self::readAttribute($fieldMapping, 'constraints')[0]);
    }

    public function testUrlFactory()
    {
        $fieldMapping = FieldMappingFactory::url();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);
        $this->assertInstanceOf(UrlConstraint::class, self::readAttribute($fieldMapping, 'constraints')[0]);
    }

    public function testIntegerFactoryWithoutConstraints()
    {
        $fieldMapping = FieldMappingFactory::integer();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(IntegerFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testIntegerFactoryWithConstraints()
    {
        $fieldMapping = FieldMappingFactory::integer(1, 3, 2);
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(IntegerFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('2', 'step', $constraints[2]);
        $this->assertAttributeEquals('1', 'base', $constraints[2]);
    }

    public function testFloatFactoryWithoutConstraints()
    {
        $fieldMapping = FieldMappingFactory::float();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(FloatFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testFloatFactoryWithConstraints()
    {
        $fieldMapping = FieldMappingFactory::float(1.5, 3., 0.5);
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(FloatFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1.5', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('0.5', 'step', $constraints[2]);
        $this->assertAttributeEquals('1.5', 'base', $constraints[2]);
    }

    public function testDecimalFactoryWithoutConstraints()
    {
        $fieldMapping = FieldMappingFactory::decimal();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DecimalFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testDecimalFactoryWithConstraints()
    {
        $fieldMapping = FieldMappingFactory::decimal('1.5', '3', '0.5');
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DecimalFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1.5', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('0.5', 'step', $constraints[2]);
        $this->assertAttributeEquals('1.5', 'base', $constraints[2]);
    }

    public function testBooleanFactory()
    {
        $fieldMapping = FieldMappingFactory::boolean();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(BooleanFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    public function testDefaultTimeFactory()
    {
        $fieldMapping = FieldMappingFactory::time();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TimeFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('UTC', $timeZone->getName());
    }

    public function testTimeFactoryWithOptions()
    {
        $fieldMapping = FieldMappingFactory::time(new DateTimeZone('Europe/Berlin'));
        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('Europe/Berlin', $timeZone->getName());
    }

    public function testDateFactory()
    {
        $fieldMapping = FieldMappingFactory::date();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DateFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('UTC', $timeZone->getName());
    }

    public function testDateFactoryWithOptions()
    {
        $fieldMapping = FieldMappingFactory::date(new DateTimeZone('Europe/Berlin'));
        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('Europe/Berlin', $timeZone->getName());
    }

    public function testDateTimeFactory()
    {
        $fieldMapping = FieldMappingFactory::dateTime();
        $this->assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DateTimeFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('UTC', $timeZone->getName());
        $this->assertAttributeSame(false, 'localTime', $formatter);
    }

    public function testDateTimeFactoryWithOptions()
    {
        $fieldMapping = FieldMappingFactory::dateTime(new DateTimeZone('Europe/Berlin'), true);
        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone = self::readAttribute($formatter, 'timeZone');
        $this->assertSame('Europe/Berlin', $timeZone->getName());
        $this->assertAttributeSame(true, 'localTime', $formatter);
    }
}
