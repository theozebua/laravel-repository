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

            $this->processParameter($parameter, $arrangedParameter);

            return $arrangedParameter;
        })->join(', ');
    }

    protected function processParameter(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        $this->processParameterType($parameter, $arrangedParameter);
        $this->handlePassedByReference($parameter, $arrangedParameter);
        $this->processParameterName($parameter, $arrangedParameter);
        $this->handleDefaultValue($parameter, $arrangedParameter);
    }

    protected function processParameterType(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        if ($parameter->hasType()) {
            $this->processType($arrangedParameter, $parameter->getType());

            $arrangedParameter .= ' ';
        }
    }

    protected function handlePassedByReference(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        if (!$parameter->canBePassedByValue()) {
            $arrangedParameter .= self::AMPERSAND;
        }
    }

    protected function processParameterName(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        $arrangedParameter .= "\${$parameter->getName()}";
    }

    protected function handleDefaultValue(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        if ($parameter->isDefaultValueAvailable()) {
            $arrangedParameter .= ' = ';

            $this->processDefaultValue($parameter, $arrangedParameter);
        }
    }

    protected function processDefaultValue(\ReflectionParameter $parameter, string &$arrangedParameter): void
    {
        if ($parameter->isDefaultValueConstant()) {
            $arrangedParameter .= "\\{$parameter->getDefaultValueConstantName()}";

            return;
        }

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
