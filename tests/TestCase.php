<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\Finder\SplFileInfo;
use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\InterfaceGenerator;

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
            InterfaceGenerator::make(FileTypeEnum::INTERFACE, $interface)
                ->generate();
        });
    }

    protected function destroyDummyInterfaces(): void
    {
        Collection::make($this->interfaces)->each(function (string $interface): void {
            InterfaceGenerator::make(FileTypeEnum::INTERFACE, $interface)
                ->destroy();
        });
    }

    protected function existingInterfaces(): Collection
    {
        return Collection::make(File::allFiles(Config::get('laravel-repository.directories.interfaces')))
            ->map(function (SplFileInfo $splFileInfo): string {
                return Str::of($splFileInfo->getPathname())
                    ->className(Config::get('laravel-repository.delimiter'))
                    ->value();
            });
    }
}
