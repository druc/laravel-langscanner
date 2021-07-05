<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\FileTranslations;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LangscannerInitCommand extends Command
{
    protected $signature = 'langscanner:init';
    protected $description = "The init command will move all en php translations into an en.json file";

    public function handle()
    {
        $directories = array_filter(File::directories(resource_path('lang')), function ($path) {
            return !Str::endsWith($path, 'vendor');
        });

        if (!count($directories)) {
            $this->info('Done.');
            return;
        }

        foreach ($directories as $directory) {
            $langFiles = File::allFiles($directory);
            $translations = [];

            foreach ($langFiles as $file) {
                $translations[$file->getFilenameWithoutExtension()] = File::getRequire($file->getRealPath());
            }

            $translations = array_filter(Arr::dot($translations), function ($item) {
                return is_string($item);
            });

            (new FileTranslations("{$directory}.json"))->update($translations);
        }

        $answer = $this->ask('Delete old PHP translations directories? (y/n)');

        if (strtolower($answer) === 'y') {
            foreach ($directories as $directory) {
                File::deleteDirectory($directory);
            }
        }

        $this->info('Done.');
    }
}
