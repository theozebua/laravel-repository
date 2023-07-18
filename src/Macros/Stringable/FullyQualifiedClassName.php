<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Macros\Stringable;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

final class FullyQualifiedClassName
{
    public function __invoke(): Closure
    {
        return function (string $delimiter = '', string $extension = '.php'): Stringable {
            /** @var Stringable $this */
            return $this->before($extension)
                ->after($delimiter)
                ->prepend(Str::ucfirst($delimiter))
                ->replace('/', '\\');
        };
    }
}
