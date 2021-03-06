<?php

namespace Sanpi\Behatch\Json;

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class JsonInspector
{
    private $evaluationMode;

    private $accessor;

    public function __construct($evaluationMode)
    {
        $this->evaluationMode = $evaluationMode;
        $this->accessor = new PropertyAccessor(false, true);
    }

    public function evaluate(Json $json, $expression)
    {
        if ($this->evaluationMode === 'javascript') {
            $expression = str_replace('->', '.', $expression);
        }

        try {
            return $json->read($expression, $this->accessor);
        } catch (\Exception $e) {
            throw new \Exception("Failed to evaluate expression '$expression'");
        }
    }

    public function validate(Json $json, JsonSchema $schema)
    {
        return $schema
            ->resolve(new RefResolver(new UriRetriever(), new UriResolver()))
            ->validate($json, new Validator)
        ;
    }
}
