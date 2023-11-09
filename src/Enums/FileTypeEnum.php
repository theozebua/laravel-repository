<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Enums;

use Illuminate\Support\Collection;

enum FileTypeEnum: string
{
    case INTERFACE = 'Interface';

    case REPOSITORY = 'Repository';

    public static function list(): array
    {
        return Collection::make(self::cases())
            ->map(fn (self $fileTypeEnum) => $fileTypeEnum->value)
            ->toArray();
    }
}
