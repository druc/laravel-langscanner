<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\MergeJsonTranslationKeys;
use Druc\Langscanner\RequiredLanguages;
use Druc\Langscanner\RequiredTranslations;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class LangscannerCommand extends Command
{
    protected $signature = 'langscanner {language?}';
    protected $description = "Finds keys without a corresponding translation and writes them into the translation (json) files.";

    public function handle()
    {
        $requiredTranslations = (new RequiredTranslations(
            new Filesystem,
            config('langscanner.paths'),
            config('langscanner.excluded_paths'),
            config('langscanner.translation_methods')
        ))->toArray();

        if ($this->argument('language')) {
            $languages = [$this->argument('language')];
        } else {
            $languages = (new RequiredLanguages(
                new Filesystem,
                resource_path('lang'),
                config('langscanner.excluded_languages')
            ))->toArray();
        }

        $headers = ["Language", "Key", "Path"];
        $rows = [];

        foreach ($languages as $language) {
            $existingTranslations = json_decode(File::get(resource_path("lang/$language.json")), true);

            $missingTranslations = $this->missingTranslations($requiredTranslations, $existingTranslations);

            foreach ($missingTranslations as $key => $path) {
                $rows[] = [$language, $key, $path];
            }

            // sets missing translation keys to empty string
            $missingTranslations = array_fill_keys(array_keys($missingTranslations), '');

            (new FileTranslations(resource_path("lang/$language.json")))->update($missingTranslations);
        }

        (new MergeJsonTranslationKeys())->merge();

        $this->table($headers, $rows);
    }

    private function missingTranslations(array $required, array $existing): array
    {
        $missing = [];

        foreach ($required as $key => $value) {
            if (empty($existing[$key])) {
                $missing[$key] = $value;
            }
        }

        return $missing;
    }
}
