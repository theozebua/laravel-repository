<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Theozebua\LaravelRepository\Enums\StubEnum;

final class InterfaceGenerator extends Generator
{
    protected bool $wantsToExtend;

    public function __construct(
        private Stringable|string|null $file = null,
    ) {
        parent::__construct();

        $this->setFile(
            Config::get('laravel-repository.directories.interfaces')
                . '/'
                . Str::of($file)
                . '.php',
        );

        $directory = $this->file()->replace('\\', '/')->beforeLast('/')->value();

        if (!File::exists($directory)) {
            File::makeDirectory($directory, recursive: true);
        }
    }

    public function generate(bool $withInterfaces = false): void
    {
        $this->wantsToExtend = $withInterfaces;

        $this->wantsToExtend
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
        $this->setStub(StubEnum::INTERFACE_EXTENDS->value);

        $fullyQualifiedClassName = $this->file()->className(
            Config::get('laravel-repository.delimiter'),
        );

        $contents = Str::of(File::get($this->stub()))
            ->replace(
                [
                    '{{ NAMESPACE }}',
                    '{{ INTERFACE }}',
                    '{{ USE_STATEMENTS }}',
                    '{{ INTERFACES }}',
                ],
                [
                    $fullyQualifiedClassName->beforeLast('\\'),
                    $fullyQualifiedClassName->afterLast('\\'),
                    $this->chosenInterfaces()->map(
                        callback: fn (string $interface): string => "use {$interface};"
                    )->join("\n"),
                    $this->chosenInterfaces()->map(
                        callback: fn (string $interface): string => Str::of($interface)
                            ->afterLast('\\')
                            ->value(),
                    )->join(', '),
                ],
            );

        File::put($this->file()->value(), $contents);
    }

    protected function withoutInterfaces(): void
    {
        $this->setStub(StubEnum::INTERFACE->value);

        $fullyQualifiedClassName = $this->file()->className(
            Config::get('laravel-repository.delimiter'),
        );

        $contents = Str::of(File::get($this->stub()))
            ->replace(
                [
                    '{{ NAMESPACE }}',
                    '{{ INTERFACE }}',
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
}
