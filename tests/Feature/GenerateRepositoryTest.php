<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Tests\Feature;

use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\RepositoryGenerator;
use Theozebua\LaravelRepository\Support\File;
use Theozebua\LaravelRepository\Tests\TestCase;

final class GenerateRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->destroyDummyRepositories();
        $this->destroyDummyInterfaces();

        parent::tearDown();
    }

    public function testRepositoryIsAlreadyExists(): void
    {
        $this->generateDummyRepositories();

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::REPOSITORY->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your repository?', $this->repositories[0])
            ->expectsOutputToContain('is already exists');
    }

    public function testGenerateFirstRepository(): void
    {
        $repository = 'TestingRepository';

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::REPOSITORY->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your repository?', $repository)
            ->expectsOutputToContain('created successfully');

        $path = $this->getRepositoryPath($repository . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($repository, $contents);

        RepositoryGenerator::make($repository)->destroy();
    }

    public function testGenerateInterfaceWithoutImplementingAnyInterfaces(): void
    {
        $this->generateDummyInterfaces();

        $repository = 'TestingRepository';

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::REPOSITORY->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your repository?', $repository)
            ->expectsConfirmation('Do you want to implements some interfaces?')
            ->expectsOutputToContain('created successfully');

        $path = $this->getRepositoryPath($repository . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($repository, $contents);

        RepositoryGenerator::make($repository)->destroy();
    }

    public function testGenerateRepositoryAndImplementsSomeInterfaces(): void
    {
        $this->generateDummyInterfaces();

        $repository = 'TestingRepository';
        $existingInterfaces = $this->existingInterfaces()->toArray();

        $this->artisan('repository:generate')
            ->expectsChoice(
                'What do you want to generate?',
                FileTypeEnum::REPOSITORY->value,
                FileTypeEnum::list(),
            )
            ->expectsQuestion('What is the name of your repository?', $repository)
            ->expectsConfirmation('Do you want to implements some interfaces?', 'yes')
            ->expectsChoice(
                'Please choose interface(s) that you want to implements separated by comma',
                [$existingInterfaces[0], $existingInterfaces[1]],
                $existingInterfaces,
            )
            ->expectsOutputToContain('created successfully');

        $path = $this->getRepositoryPath($repository . '.php');
        $contents = File::get($path);

        $this->assertFileExists($path);
        $this->assertStringContainsString($repository, $contents);
        $this->assertStringContainsString($existingInterfaces[0], $contents);
        $this->assertStringContainsString($existingInterfaces[1], $contents);

        RepositoryGenerator::make($repository)->destroy();
    }
}
