<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Arrangers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Arranger
{
    protected function processType(
        string &$arrangedMethodOrParam,
        \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType $type,
    ) {
        if ($type->allowsNull() && $type instanceof \ReflectionNamedType) {
            $arrangedMethodOrParam .= '?';
        }

        switch (true) {
            case $type instanceof \ReflectionNamedType:
                $arrangedMethodOrParam .= $this->processTypeName($type);

                break;

            case $type instanceof \ReflectionUnionType:
                $arrangedMethodOrParam .= Collection::make($type->getTypes())->map(function (\ReflectionNamedType $type): string {
                    return $this->processTypeName($type);
                })->join('|');

                break;

            case $type instanceof \ReflectionIntersectionType:
                $arrangedMethodOrParam .= Collection::make($type->getTypes())->map(function (\ReflectionNamedType $type): string {
                    return $this->processTypeName($type);
                })->join('&');

                break;
        }
    }

    protected function processTypeName(\ReflectionNamedType $type): string
    {
        $name = $type->getName();

        if ($type->isBuiltin()) {
            return $name;
        }

        return sprintf('\\%s', Str::of($name)->className());
    }
}
