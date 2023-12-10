<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Support;

use Theozebua\LaravelRepository\Enums\UseStatementType;

final class UseStatementsHolder
{
    private static array $useStatements = [];

    public static function add(string $useStatement, ?UseStatementType $type = null): void
    {
        static::$useStatements[] = [
            'type' => $type ?? UseStatementType::CLASSNAME,
            'value' => $useStatement,
        ];
    }

    public static function get(?string $key = null): array
    {
        if (is_null($key)) {
            return static::$useStatements;
        }

        return static::$useStatements[$key] ?? [];
    }
}
