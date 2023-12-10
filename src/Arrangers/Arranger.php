<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Arrangers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Theozebua\LaravelRepository\Support\UseStatementsHolder;

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

    protected function processType(string &$arranged, \ReflectionType $type)
    {
        if ($type->allowsNull() && $type instanceof \ReflectionNamedType) {
            $arranged .= self::QUESTION_MARK;
        }

        if ($type instanceof \ReflectionNamedType) {
            $arranged .= $this->processTypeName($type);
        }

        $union = $type instanceof \ReflectionUnionType;
        $intersection = $type instanceof \ReflectionIntersectionType;

        if ($union || $intersection) {
            /** @var \ReflectionUnionType|\ReflectionIntersectionType $type */
            $arranged .= Collection::make($type->getTypes())->map(function (\ReflectionNamedType $type): string {
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

        UseStatementsHolder::add($name);

        return Str::of($name)->classBasename()->value();
    }
}
