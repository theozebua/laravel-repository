<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait PathTrait
{
    protected function getInterfacePath(string $path = ''): string
    {
        return Config::get('laravel-repository.directories.interfaces')
            . '/'
            . Str::of($path)->trim('/');
    }

    protected function getRepositoryPath(string $path = ''): string
    {
        return Config::get('laravel-repository.directories.repositories')
            . '/'
            . Str::of($path)->trim('/');
    }
}
