<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Theozebua\LaravelRepository\Enums\FileTypeEnum;

abstract class Generator implements GeneratorInterface
{
    protected string $stubPath = __DIR__ . '/Console/stubs/';

    private Collection $chosenInterfaces;
    private string $stub;

    public function __construct(
        private ?FileTypeEnum $type = null,
        private Stringable|string|null $file = null,
    ) {
        //
    }

    final public static function make(FileTypeEnum $type, string $file): static
    {
        return new static($type, $file);
    }

    final public function setChosenInterfaces(Collection $interfaces): static
    {
        $this->chosenInterfaces = $interfaces;

        return $this;
    }

    final public function chosenInterfaces(): Collection
    {
        return $this->chosenInterfaces;
    }

    final protected function setStub(string $stub): static
    {
        $this->stub = $this->stubPath . $stub;

        return $this;
    }

    final protected function stub(): string
    {
        return $this->stub;
    }

    final protected function setType(FileTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    final protected function type(): FileTypeEnum
    {
        return $this->type;
    }

    final protected function setFile(string $file): static
    {
        $this->file = Str::of($file);

        return $this;
    }

    final protected function file(): Stringable
    {
        return $this->file;
    }
}
