<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Arrangers;

use Illuminate\Support\Collection;

final class MethodArranger extends Arranger implements ArrangerInterface
{
    /**
     * @param Collection<int, \ReflectionMethod> $methods
     */
    public function __construct(private Collection $methods)
    {
        //
    }

    /**
     * @param Collection<int, \ReflectionMethod> $methods
     */
    public static function from(Collection $methods): static
    {
        return new static($methods);
    }

    public function arrange(): string
    {
        return $this->methods->map(function (\ReflectionMethod $method): string {
            $arrangedMethod = '';

            $this->processMethod($method, $arrangedMethod);

            return $arrangedMethod;
        })->join(PHP_EOL . PHP_EOL);
    }

    protected function processMethod(\ReflectionMethod $method, string &$arrangedMethod): void
    {
        $this->processVisibility($arrangedMethod);
        $this->handleStatic($method, $arrangedMethod);
        $this->processMethodName($method, $arrangedMethod);
        $this->processParameters(Collection::make($method->getParameters()), $arrangedMethod);
        $this->closeParameter($arrangedMethod);
        $this->processReturnTypes($method, $arrangedMethod);
        $this->processBody($arrangedMethod);
    }

    protected function processVisibility(string &$arrangedMethod): void
    {
        $arrangedMethod = PHP_TAB . 'public ';
    }

    protected function handleStatic(\ReflectionMethod $method, string &$arrangedMethod): void
    {
        if ($method->isStatic()) {
            $arrangedMethod .= 'static ';
        }
    }

    protected function processMethodName(\ReflectionMethod $method, string &$arrangedMethod): void
    {
        $arrangedMethod .= "function {$method->getName()}(";
    }

    protected function processParameters(Collection $parameters, string &$arrangedMethod): void
    {
        if ($parameters->isNotEmpty()) {
            $arrangedMethod .= ParameterArranger::from($parameters)->arrange();
        }
    }

    protected function closeParameter(string &$arrangedMethod): void
    {
        $arrangedMethod .= ')';
    }

    protected function processReturnTypes(\ReflectionMethod $method, string &$arrangedMethod): void
    {
        if ($method->hasReturnType()) {
            $arrangedMethod .= ": ";

            $this->processType($arrangedMethod, $method->getReturnType());
        }
    }

    protected function processBody(string &$arrangedMethod): void
    {
        $arrangedMethod .= PHP_EOL . PHP_TAB . '{';
        $arrangedMethod .= PHP_EOL . PHP_TAB . PHP_TAB . sprintf("throw new \\Exception('%s');", 'Implement me...');
        $arrangedMethod .= PHP_EOL . PHP_TAB . '}';
    }
}
