<?php
declare(strict_types = 1);

namespace vardumper\Formidable\Mapping;

use vardumper\Formidable\Data;
use vardumper\Formidable\FormError\FormErrorSequence;
use vardumper\Formidable\Mapping\Exception\InvalidTypeException;

final class RepeatedMapping implements MappingInterface
{
    use MappingTrait;

    /**
     * @var MappingInterface
     */
    private $wrappedMapping;

    /**
     * @var string
     */
    private $key = '';

    /**
     * @param MappingInterface $wrappedMapping
     */
    public function __construct(MappingInterface $wrappedMapping)
    {
        $this->wrappedMapping = $wrappedMapping;
    }

    public function bind(Data $data) : BindResult
    {
        $values = [];
        $formErrorSequence = new FormErrorSequence();

        foreach ($data->getIndexes($this->key) as $index) {
            $bindResult = $this->wrappedMapping->withPrefixAndRelativeKey($this->key, $index)->bind($data);

            if (!$bindResult->isSuccess()) {
                $formErrorSequence = $formErrorSequence->merge($bindResult->getFormErrorSequence());
                continue;
            }

            $values[] = $bindResult->getValue();
        }

        if (!$formErrorSequence->isEmpty()) {
            return BindResult::fromFormErrorSequence($formErrorSequence);
        }

        return $this->applyConstraints($values, $this->key);
    }

    public function unbind($value) : Data
    {
        if (!is_array($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'array');
        }

        $data = Data::none();

        foreach ($value as $index => $individualValue) {
            $data = $data->merge(
                $this->wrappedMapping
                ->withPrefixAndRelativeKey($this->key, (string) $index)
                ->unbind($individualValue)
            );
        }

        return $data;
    }

    public function withPrefixAndRelativeKey(string $prefix, string $relativeKey) : MappingInterface
    {
        $clone = clone $this;
        $clone->key = $this->createKeyFromPrefixAndRelativeKey($prefix, $relativeKey);
        return $clone;
    }
}
