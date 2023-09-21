<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Theozebua\LaravelRepository\Enums\StubEnum;
use Theozebua\LaravelRepository\Arrangers\MethodArranger;

final class RepositoryGenerator extends Generator implements GeneratorInterface
{
    use PathTrait;

    protected bool $wantsToImplement;

    protected Collection $useStatements;

    protected static bool $fake = false;

    public function __construct(
        private Stringable|string|null $file = null,
    ) {
        parent::__construct();

        $this->setFile($this->getRepositoryPath($file . '.php'));

        $directory = $this->file()->replace('\\', '/')->beforeLast('/')->value();

        if (!File::exists($this->getInterfacePath())) {
            File::makeDirectory($this->getInterfacePath(), recursive: true);
        }

        if (!File::exists($directory)) {
            File::makeDirectory($directory, recursive: true);
        }
    }

    public static function fake(): void
    {
        static::$fake = true;
    }

    public function generate(bool $withInterfaces = false): void
    {
        $this->wantsToImplement = $withInterfaces;

        $this->wantsToImplement
            ? $this->withInterfaces()
            : $this->withoutInterfaces();
    }

    public function destroy(): void
    {
        if (File::exists($this->file()->value())) {
            File::delete($this->file()->value());
        }
    }

    protected function withInterfaces(): void
    {
        $this->setStub(StubEnum::REPOSITORY_IMPLEMENTS->value);

        $fullyQualifiedClassName = $this->file()->className(
            Config::get('laravel-repository.delimiter'),
        );

        $this->useStatements = $this->chosenInterfaces();

        $contents = Str::of(File::get($this->stub()))
            ->replace(
                [
                    '{{ NAMESPACE }}',
                    '{{ REPOSITORY }}',
                    '{{ USE_STATEMENTS }}',
                    '{{ INTERFACES }}',
                    '{{ METHODS }}',
                ],
                [
                    $fullyQualifiedClassName->beforeLast('\\'),
                    $fullyQualifiedClassName->afterLast('\\'),
                    $this->useStatements->map(
                        callback: fn (string $interface): string => "use {$interface};"
                    )->join(PHP_EOL),
                    $this->chosenInterfaces()->map(
                        callback: fn (string $interface): string => Str::of($interface)
                            ->afterLast('\\')
                            ->value(),
                    )->join(', '),
                    $this->implementsMethods(),
                ],
            );

        File::put($this->file()->value(), $contents);
    }

    protected function withoutInterfaces(): void
    {
        $this->setStub(StubEnum::REPOSITORY->value);

        $fullyQualifiedClassName = $this->file()->className(
            Config::get('laravel-repository.delimiter'),
        );

        $contents = Str::of(File::get($this->stub()))
            ->replace(
                [
                    '{{ NAMESPACE }}',
                    '{{ REPOSITORY }}',
                ],
                [
                    $fullyQualifiedClassName
                        ->beforeLast('\\')
                        ->value(),
                    $fullyQualifiedClassName
                        ->afterLast('\\')
                        ->value(),
                ],
            );

        File::put($this->file()->value(), $contents);
    }

    protected function implementsMethods(): string
    {
        if (static::$fake) {
            return '';
        }

        return $this->chosenInterfaces()->map(function (string $interface): string {
            $methods = Collection::make((new \ReflectionClass($interface))->getMethods());

            return $methods->isNotEmpty()
                ? MethodArranger::from($methods)->arrange()
                : '//';
        })->join(PHP_EOL);
    }
}
