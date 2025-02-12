<?php
declare(strict_types = 1);

namespace vardumper\Formidable\Transformer;

final class CallbackTransformer implements TransformerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(string $value, string $key) : string
    {
        $callback = $this->callback;
        return $callback($value, $key);
    }
}
