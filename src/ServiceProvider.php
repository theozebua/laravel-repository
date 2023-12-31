<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Stringable;
use Theozebua\LaravelRepository\Console\Commands\RepositoryMakeCommand;
use Theozebua\LaravelRepository\Macros\Collection\Recursive;
use Theozebua\LaravelRepository\Macros\Stringable\FullyQualifiedClassName;

final class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        $this->stringableMacros()
            ->reject(fn (string $macro): bool => Stringable::hasMacro($macro))
            ->each(fn (string $macro, string $class) => Stringable::macro($macro, App::make($class)()));

        $this->collectionMacros()
            ->reject(fn (string $macro): bool => Collection::hasMacro($macro))
            ->each(fn (string $macro, string $class) => Collection::macro($macro, App::make($class)()));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/config/laravel-repository.php', 'laravel-repository');

        $this->publishes([
            __DIR__ . '/config/laravel-repository.php' => $this->app->configPath('laravel-repository.php'),
        ], 'laravel-repository-config');
    }

    protected function stringableMacros(): Collection
    {
        return Collection::make([
            FullyQualifiedClassName::class => 'className',
        ]);
    }

    protected function collectionMacros(): Collection
    {
        return Collection::make([
            Recursive::class => 'recursive',
        ]);
    }
}
