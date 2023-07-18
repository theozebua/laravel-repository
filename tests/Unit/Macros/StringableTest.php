<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests\Unit\Macros;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Theozebua\LaravelRepository\Tests\TestCase;

final class StringableTest extends TestCase
{
    public function testGetFullyQualifiedClassNameFromGivenPath(): void
    {
        $src = Config::get('laravel-repository.directories.interfaces') . '/RepositoryInterface.php';
        $expected = 'App\Repositories\Interfaces\RepositoryInterface';
        $actual = Str::of($src)->className(Config::get('laravel-repository.delimiter'))->value();

        $this->assertSame($expected, $actual);
    }
}
