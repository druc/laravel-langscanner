<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\FileTranslations;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class LangscannerInitCommand extends Command
{
    protected $signature = 'langscanner:init';
    protected $description = "The init command will move all en php translations into an en.json file";

    public function handle()
    {
        if (!File::isDirectory(config('langscanner.languages_path').'/en')) {
            $this->info(config('langscanner.languages_path').'/en' . ' does not exist.');
            return;
        }

        App::setLocale('en');

        $langFiles = (new Filesystem())->allFiles(config('langscanner.languages_path').'/en');

        $translations = [];

        foreach ($langFiles as $file) {
            $translations[$file->getFilenameWithoutExtension()] = __($file->getFilenameWithoutExtension());
        }

        $translations = array_filter(Arr::dot($translations), function ($item) {
            return is_string($item);
        });

        (new FileTranslations(config('langscanner.languages_path')."/en.json"))
            ->update($translations);

        if ($answer = $this->ask('Delete lang/en directory? (y/n)')) {
            if (strtolower($answer) === 'y') {
                File::deleteDirectory(config("langscanner.languages_path").'/en');
                $this->info('Directory deleted.');
            } else {
                $this->info('Directory has been kept.');
            }
        }
    }
}
