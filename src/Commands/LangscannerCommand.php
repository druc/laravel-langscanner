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
    protected $signature = 'langscanner';
    protected $description = "Finds keys without a corresponding translations and writes them into the translation (json) files.";

    public function handle()
    {
        $headers = ["Language", "Key", "Path"];
        $rows = [];

        $languages = new RequiredLanguages(
            new Filesystem,
            config('langscanner.languages_path'),
            config('langscanner.excluded_languages')
        );

        $requiredTranslations = new RequiredTranslations(
            new Filesystem,
            config('langscanner.paths'),
            config('langscanner.excluded_paths'),
            config('langscanner.translation_methods')
        );

        foreach ($languages->toArray() as $language) {
            $missingTranslations = new MissingTranslations(
                $requiredTranslations,
                new ExistingTranslations(
                    new Filesystem,
                    config('langscanner.languages_path'),
                    $language
                )
            );

            foreach ($missingTranslations->toArray() as $key => $path) {
                $rows[] = [$language, $key, $path];
            }

            (new FileTranslations(config('langscanner.languages_path')."/$language.json"))
                ->update($missingTranslations);
        }

        $this->table($headers, $rows);
    }
}
