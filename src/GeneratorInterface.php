<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository;

interface GeneratorInterface
{
    public function generate(bool $withInterface = false): void;
}
