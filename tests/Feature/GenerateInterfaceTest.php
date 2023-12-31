<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests\Feature;

use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\InterfaceGenerator;
use Theozebua\LaravelRepository\Support\File;
use Theozebua\LaravelRepository\Tests\TestCase;

final class GenerateInterfaceTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->destroyDummyInterfaces();

        parent::tearDown();
    }

    public function testInterfaceIsAlreadyExists(): void
    {
        $this->generateDummyInterfaces();

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::INTERFACE->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your interface?', $this->interfaces[0])
            ->expectsOutputToContain('is already exists');
    }

    public function testGenerateFirstInterface(): void
    {
        $interface = 'TestingInterface';

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::INTERFACE->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your interface?', $interface)
            ->expectsOutputToContain('created successfully');

        $path = $this->getInterfacePath($interface . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($interface, $contents);

        InterfaceGenerator::make($interface)->destroy();
    }

    public function testGenerateInterfaceWithoutExtendingAnotherInterfaces(): void
    {
        $this->generateDummyInterfaces();

        $interface = 'TestingInterface';

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::INTERFACE->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your interface?', $interface)
            ->expectsConfirmation('Do you want to extends another interfaces?')
            ->expectsOutputToContain('created successfully');

        $path = $this->getInterfacePath($interface . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($interface, $contents);

        InterfaceGenerator::make($interface)->destroy();
    }

    public function testGenerateInterfaceAndExtendsAnotherInterfaces(): void
    {
        $this->generateDummyInterfaces();

        $interface = 'TestingInterface';
        $existingInterfaces = $this->existingInterfaces()->toArray();

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::INTERFACE->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your interface?', $interface)
            ->expectsConfirmation('Do you want to extends another interfaces?', 'yes')
            ->expectsChoice(
                'Please choose interface(s) that you want to extends separated by comma',
                [$existingInterfaces[0], $existingInterfaces[1]],
                $existingInterfaces,
            )
            ->expectsOutputToContain('created successfully');

        $path = $this->getInterfacePath($interface . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($interface, $contents);
        $this->assertStringContainsString($existingInterfaces[0], $contents);
        $this->assertStringContainsString($existingInterfaces[1], $contents);

        InterfaceGenerator::make($interface)->destroy();
    }
}
