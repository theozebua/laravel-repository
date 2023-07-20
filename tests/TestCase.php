<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests;

use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Theozebua\LaravelRepository\InterfaceGenerator;
use Theozebua\LaravelRepository\PathTrait;
use Theozebua\LaravelRepository\RepositoryGenerator;
use Theozebua\LaravelRepository\Support\File;

abstract class TestCase extends BaseTestCase
{
    use PathTrait;

    protected $enablesPackageDiscoveries = true;

    protected array $interfaces = [
        'RepositoryInterface',
        'Another/DummyInterface',
        'Nested/Directory/InsideInterface',
    ];

    protected array $repositories = [
        'RepositoryImplementation',
        'Another/DummyImplementation',
        'Nested/Directory/InsideImplementation',
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

    protected function generateDummyRepositories(): void
    {
        Collection::make($this->repositories)->each(function (string $repository): void {
            RepositoryGenerator::make($repository)->generate();
        });
    }

    protected function destroyDummyRepositories(): void
    {
        Collection::make($this->repositories)->each(function (string $repository): void {
            RepositoryGenerator::make($repository)->destroy();
        });
    }
}
