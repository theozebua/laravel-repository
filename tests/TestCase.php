<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Theozebua\LaravelRepository\InterfaceGenerator;
use Theozebua\LaravelRepository\Support\File;

abstract class TestCase extends BaseTestCase
{
    protected $enablesPackageDiscoveries = true;

    protected array $interfaces = [
        'RepositoryInterface',
        'Another/DummyInterface',
        'Nested/Directory/InsideInterface',
    ];

    protected function generateDummyInterfaces(): void
    {
        Collection::make($this->interfaces)->each(function (string $interface): void {
            InterfaceGenerator::make($interface)->generate();
        });
    }

    protected function destroyDummyInterfaces(): void
    {
        Collection::make($this->interfaces)->each(function (string $interface): void {
            InterfaceGenerator::make($interface)->destroy();
        });
    }

    protected function existingInterfaces(): Collection
    {
        return File::existingInterfaces();
    }

    protected function getInterfacePath(string $path): string
    {
        return Config::get('laravel-repository.directories.interfaces')
            . '/'
            . Str::of($path)->trim('/');
    }
}
