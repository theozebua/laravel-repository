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
        return $this->methods->map(function (\ReflectionMethod $method, int $key): string {
            $parameters = Collection::make($method->getParameters());

            $arrangedMethod = '';

            $arrangedMethod = PHP_TAB . 'public ';

            if ($method->isStatic()) {
                $arrangedMethod .= 'static ';
            }

            $arrangedMethod .= "function {$method->getName()}(";

            if ($parameters->isNotEmpty()) {
                $arrangedMethod .= ParameterArranger::from($parameters)->arrange();
            }

            $arrangedMethod .= ')';

            if ($method->hasReturnType()) {
                $arrangedMethod .= ": ";

                $returnType = $method->getReturnType();

                $this->processType($arrangedMethod, $returnType);
            }

            $arrangedMethod .= PHP_EOL . PHP_TAB . '{';
            $arrangedMethod .= PHP_EOL . PHP_TAB . PHP_TAB . sprintf("throw new \\Exception('%s');", 'Implement me...');
            $arrangedMethod .= PHP_EOL . PHP_TAB . '}';

            return $arrangedMethod;
        })->join(PHP_EOL . PHP_EOL);
    }
}
