<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests\Feature;

use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\InterfaceGenerator;
use Theozebua\LaravelRepository\Tests\TestCase;

final class GenerateInterfaceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->destroyDummyInterfaces();
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
            ->expectsQuestion('What is the name of your interface?', $interface);

        InterfaceGenerator::make(FileTypeEnum::INTERFACE, $interface)
            ->destroy();
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
            ->expectsConfirmation('Do you want to extends another interfaces?');

        InterfaceGenerator::make(FileTypeEnum::INTERFACE, $interface)
            ->destroy();
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
            );

        InterfaceGenerator::make(FileTypeEnum::INTERFACE, $interface)
            ->destroy();
    }
}
