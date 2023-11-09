<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\InterfaceGenerator;
use Theozebua\LaravelRepository\RepositoryGenerator;
use Theozebua\LaravelRepository\Support\File;

final class RepositoryMakeCommand extends Command
{
    protected $signature = 'repository:generate';

    protected $description = 'Create a new repository file (Interface or Implementation)';

    public function handle(): void
    {
        $type = $this->components->choice('What do you want to generate?', FileTypeEnum::list());

        switch ($type) {
            case FileTypeEnum::INTERFACE->value:
                $this->generateInterface();

                break;

            case FileTypeEnum::REPOSITORY->value:
                $this->generateRepository();

                break;
        }
    }

    protected function generateInterface(): void
    {
        $path = Config::get('laravel-repository.directories.interfaces');

        $name = $this->components->ask('What is the name of your interface?', 'RepositoryInterface');

        $file = $path . '/' . $name . '.php';

        if (File::exists($file)) {
            $this->components->error(sprintf('[%s] is already exists', $file));

            return;
        }

        $generator = InterfaceGenerator::make($name);

        $existingInterfaces = File::existingInterfaces();

        if (
            $existingInterfaces->isNotEmpty() &&
            $this->components->confirm('Do you want to extends another interfaces?')
        ) {
            $chosenInterfaces = Collection::make(
                $this->components->choice(
                    'Please choose interface(s) that you want to extends separated by comma',
                    $existingInterfaces->toArray(),
                    multiple: true,
                )
            );

            $generator->setChosenInterfaces($chosenInterfaces)->generate(true);

            $this->components->info(
                sprintf('%s [%s] created successfully', FileTypeEnum::INTERFACE->value, $file)
            );

            return;
        }

        $generator->generate();

        $this->components->info(
            sprintf('%s [%s] created successfully', FileTypeEnum::INTERFACE->value, $file)
        );
    }

    protected function generateRepository(): void
    {
        $path = Config::get('laravel-repository.directories.repositories');

        $name = $this->components->ask('What is the name of your repository?', 'Repository');

        $file = $path . '/' . $name . '.php';

        if (File::exists($file)) {
            $this->components->error(sprintf('[%s] is already exists', $file));

            return;
        }

        $generator = RepositoryGenerator::make($name);

        $existingInterfaces = File::existingInterfaces();

        if (
            $existingInterfaces->isNotEmpty() &&
            $this->components->confirm('Do you want to implements some interfaces?')
        ) {
            $chosenInterfaces = Collection::make(
                $this->components->choice(
                    'Please choose interface(s) that you want to implements separated by comma',
                    $existingInterfaces->toArray(),
                    multiple: true,
                )
            );

            $generator->setChosenInterfaces($chosenInterfaces)->generate(true);

            $this->components->info(
                sprintf('%s [%s] created successfully', FileTypeEnum::REPOSITORY->value, $file)
            );

            return;
        }

        $generator->generate();

        $this->components->info(
            sprintf('%s [%s] created successfully', FileTypeEnum::REPOSITORY->value, $file)
        );
    }
}
