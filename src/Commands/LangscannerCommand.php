<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\ExistingTranslations;
use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\MissingTranslations;
use Druc\Langscanner\RequiredLanguages;
use Druc\Langscanner\RequiredTranslations;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LangscannerCommand extends Command
{
    protected $signature = 'langscanner {language?}';
    protected $description = "Finds keys without a corresponding translation and writes them into the translation (json) files.";

    public function handle()
    {
        $headers = ["Language", "Key", "Path"];
        $rows = [];

        $requiredTranslations = new RequiredTranslations(
            new Filesystem,
            config('langscanner.paths'),
            config('langscanner.excluded_paths'),
            config('langscanner.translation_methods')
        );

        if ($this->argument('language')) {
            $languages = [$this->argument('language')];
        } else {
            $languages = (new RequiredLanguages(
                new Filesystem,
                resource_path('lang'),
                config('langscanner.excluded_languages')
            ))->toArray();
        }

        foreach ($languages as $language) {
            $missingTranslations = new MissingTranslations(
                array_merge($requiredTranslations->toArray(), json_decode(file_get_contents(resource_path('lang').'/en.json'), true)),
                (new ExistingTranslations(
                    new Filesystem,
                    resource_path('lang'),
                    $language
                ))->toArray()
            );

            foreach ($missingTranslations->toArray() as $key => $path) {
                $rows[] = [$language, $key, $path];
            }

            $missingTranslations = array_fill_keys(array_keys($missingTranslations->toArray()), '');

            (new FileTranslations(resource_path("lang/$language.json")))
                ->update($missingTranslations);
        }

        $this->table($headers, $rows);
    }
}
