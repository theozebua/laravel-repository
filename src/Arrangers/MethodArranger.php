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

            $this->processMethod($arrangedMethod, $method);

            return $arrangedMethod;
        })->join(PHP_EOL . PHP_EOL);
    }

    protected function processMethod(string &$arrangedMethod, \ReflectionMethod $method): void
    {
        $arrangedMethod = PHP_TAB . 'public ';

        if ($method->isStatic()) {
            $arrangedMethod .= 'static ';
        }

        $arrangedMethod .= "function {$method->getName()}(";

        $parameters = Collection::make($method->getParameters());

        if ($parameters->isNotEmpty()) {
            $arrangedMethod .= ParameterArranger::from($parameters)->arrange();
        }

        $arrangedMethod .= ')';

        if ($method->hasReturnType()) {
            $arrangedMethod .= ": ";

            $this->processType($arrangedMethod, $method->getReturnType());
        }

        $arrangedMethod .= PHP_EOL . PHP_TAB . '{';
        $arrangedMethod .= PHP_EOL . PHP_TAB . PHP_TAB . sprintf("throw new \\Exception('%s');", 'Implement me...');
        $arrangedMethod .= PHP_EOL . PHP_TAB . '}';
    }
}
