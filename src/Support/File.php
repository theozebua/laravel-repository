<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as LaravelFile;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

final class File extends LaravelFile
{
    public static function existingInterfaces(): Collection
    {
        return Collection::make(File::allFiles(Config::get('laravel-repository.directories.interfaces')))
            ->map(function (SplFileInfo $splFileInfo): string {
                return Str::of($splFileInfo->getPathname())
                    ->className(Config::get('laravel-repository.delimiter'))
                    ->value();
            });
    }
}
