<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Arrangers;

use Illuminate\Support\Collection;

final class ParameterArranger extends Arranger implements ArrangerInterface
{
    /**
     * @param Collection<int, \ReflectionParameter> $parameters
     */
    public function __construct(private Collection $parameters)
    {
        //
    }

    /**
     * @param Collection<int, \ReflectionParameter> $parameters
     */
    public static function from(Collection $parameters): static
    {
        return new static($parameters);
    }

    public function arrange(): string
    {
        return $this->parameters->map(function (\ReflectionParameter $parameter): string {
            $arrangedParameter = '';

            $type = $parameter->getType();

            if ($parameter->hasType()) {
                $this->processType($arrangedParameter, $type);

                $arrangedParameter .= ' ';
            }

            if (!$parameter->canBePassedByValue()) {
                $arrangedParameter .= '&';
            }

            $arrangedParameter .= "\${$parameter->getName()}";

            if ($parameter->isDefaultValueAvailable()) {
                $arrangedParameter .= ' = ';

                $this->processDefaultValue($arrangedParameter, $parameter);
            }

            return $arrangedParameter;
        })->join(', ');
    }

    protected function processDefaultValue(string &$arrangedParameter, \ReflectionParameter $parameter): void
    {
        if ($parameter->isDefaultValueConstant()) {
            $arrangedParameter .= "\\{$parameter->getDefaultValueConstantName()}";
        } else {
            $defaultValue = $parameter->getDefaultValue();

            switch (true) {
                case is_null($defaultValue):
                    $arrangedParameter .= 'null';

                    break;

                case is_string($defaultValue):
                    $arrangedParameter .= "'{$defaultValue}'";

                    break;

                case is_int($defaultValue):
                case is_float($defaultValue):
                    $arrangedParameter .= (string) $defaultValue;

                    break;

                case is_bool($defaultValue):
                    $arrangedParameter .= $defaultValue ? 'true' : 'false';

                    break;

                case is_array($defaultValue):
                    $arrangedParameter .= "[]";

                    break;

                default:
                    $arrangedParameter .= 'null';
            }
        }
    }
}
