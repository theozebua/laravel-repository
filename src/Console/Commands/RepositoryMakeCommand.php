<?php

declare(strict_types=1);

namespace Theozebua\LaravelRepository\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;
use Theozebua\LaravelRepository\Enums\FileTypeEnum;
use Theozebua\LaravelRepository\InterfaceGenerator;

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
                dd($type);
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

        $generator = InterfaceGenerator::make(FileTypeEnum::INTERFACE, $name);

        $existingInterfaces = Collection::make(File::allFiles($path))
            ->map(function (SplFileInfo $splFileInfo): string {
                return Str::of($splFileInfo->getPathname())
                    ->className(Config::get('laravel-repository.delimiter'))
                    ->value();
            });

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
}
