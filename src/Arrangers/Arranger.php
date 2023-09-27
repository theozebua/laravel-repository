<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Arrangers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Arranger
{
    /**
     * This is for union type.
     */
    public const PIPE = '|';

    /**
     * This is for intersection type or parameter that will be passed by reference.
     */
    public const AMPERSAND = '&';

    /**
     * This is for optional type that can accept or return null.
     */
    public const QUESTION_MARK = '?';

    protected function processType(
        string &$arrangedMethodOrParam,
        \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType $type,
    ) {
        if ($type->allowsNull() && $type instanceof \ReflectionNamedType) {
            $arrangedMethodOrParam .= self::QUESTION_MARK;
        }

        if ($type instanceof \ReflectionNamedType) {
            $arrangedMethodOrParam .= $this->processTypeName($type);
        }

        $union = $type instanceof \ReflectionUnionType;
        $intersection = $type instanceof \ReflectionIntersectionType;

        if ($union || $intersection) {
            $arrangedMethodOrParam .= Collection::make($type->getTypes())->map(function (\ReflectionNamedType $type): string {
                return $this->processTypeName($type);
            })->join($union ? self::PIPE : ($intersection ? self::AMPERSAND : ''));
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
