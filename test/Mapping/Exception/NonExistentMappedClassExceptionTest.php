<?php
declare(strict_types = 1);

namespace vardumper\FormidableTest\Mapping\Exception;

use vardumper\Formidable\Mapping\Exception\NonExistentMappedClassException;
use vardumper\Formidable\Mapping\MappingInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers vardumper\Formidable\Mapping\Exception\NonExistentMappedClassException
 */
class NonExistentMappedClassExceptionTest extends TestCase
{
    public function testFromNonExistentClass()
    {
        $this->assertSame(
            sprintf('Class with name foo does not exist', MappingInterface::class),
            NonExistentMappedClassException::fromNonExistentClass('foo')->getMessage()
        );
    }
}
