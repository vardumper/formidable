<?php
declare(strict_types = 1);

namespace vardumper\Formidable\Helper;

use vardumper\Formidable\Field;
use DOMDocument;

final class InputText
{
    use AttributeTrait;

    public function __invoke(Field $field, array $htmlAttributes = []) : string
    {
        if (!array_key_exists('type', $htmlAttributes)) {
            $htmlAttributes['type'] = 'text';
        }

        $htmlAttributes['id'] = 'input.' . $field->getKey();
        $htmlAttributes['name'] = $field->getKey();
        $htmlAttributes['value'] = $field->getValue();

        $document = new DOMDocument('1.0', 'utf-8');
        $input = $document->createElement('input');
        $document->appendChild($input);
        $this->addAttributes($input, $htmlAttributes);

        return $document->saveHTML($input);
    }
}
