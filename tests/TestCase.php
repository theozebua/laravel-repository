<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $enablesPackageDiscoveries = true;
}
