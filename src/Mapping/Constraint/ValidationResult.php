<?php
declare(strict_types = 1);

namespace DASPRiD\Formidable\Mapping\Constraint;

use Traversable;

final class ValidationResult
{
    /**
     * @var ValidationError[]
     */
    private $validationErrors;

    public function __construct(ValidationError ...$validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function isSuccess()
    {
        return empty($this->validationErrors);
    }

    public function merge(self $other)
    {
        $validationResult = clone $this;
        $validationResult->validationErrors = array_merge($this->validationErrors, $other->validationErrors);
        return $validationResult;
    }

    public function getValidationErrors() : Traversable
    {
        yield from $this->validationErrors;
    }
}
