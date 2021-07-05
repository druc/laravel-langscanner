<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\MergeJsonTranslationKeys;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LangscannerInitCommand extends Command
{
    protected $signature = 'langscanner:init';
    protected $description = "This will move PHP translations into dedicated JSON files";

    public function handle()
    {
        $directories = Collection::make(File::directories(resource_path('lang')))
            ->filter(function ($path) {
                return !Str::endsWith($path, 'vendor');
            });

        if (!$directories->count()) {
            $this->info('Done.');
            return;
        }

        // move php to json
        $directories->each(function ($path) {
            $translations = Collection::make(File::allFiles($path))
                ->filter(function ($file) {
                    return $file->getExtension() === 'php';
                })
                ->reduce(function ($carry, $file) {
                    return $carry->merge(Arr::dot([
                        $file->getFilenameWithoutExtension() => File::getRequire($file->getRealPath())
                    ]));
                }, collect([]))->filter(function ($item) {
                    return is_string($item);
                })->toArray();

            (new FileTranslations("{$path}.json"))->update($translations);
        });

        (new MergeJsonTranslationKeys())->merge();

        $answer = $this->ask('Delete old PHP translations directories? (y/n)');

        if (strtolower($answer) === 'y') {
            foreach ($directories as $directory) {
                File::deleteDirectory($directory);
            }
        }

        $this->info('Done.');
    }
}
