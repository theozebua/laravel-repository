<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Macros\Collection;

use Closure;
use Illuminate\Support\Collection;

final class Recursive
{
    public function __invoke(): Closure
    {
        return function (): Collection {
            /** @var Collection $this */
            return $this->map(function (mixed $value): mixed {
                /** @var Collection $this */
                return is_array($value)
                    ? $this->make($value)->recursive()
                    : $value;
            });
        };
    }
}
