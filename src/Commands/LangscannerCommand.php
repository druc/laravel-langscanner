<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\Languages;
use Druc\Langscanner\RequiredTranslations;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LangscannerCommand extends Command
{
    protected $signature = 'langscanner {language?}';
    protected $description = "Finds keys without a corresponding translation and writes them into the translation (json) files.";

    public function handle(Filesystem $filesystem)
    {
        if ($this->argument('language')) {
            $languages = [$this->argument('language')];
        } else {
            $languages = (new Languages($filesystem, resource_path('lang'), config('langscanner.excluded_languages')))->toArray();
        }

        $requiredTranslations = (new RequiredTranslations($filesystem, config('langscanner')))->toArray();

        $rows = [];

        foreach ($languages as $language) {
            if ($filesystem->exists(resource_path("lang/$language.json"))) {
                $existingTranslations = json_decode($filesystem->get(resource_path("lang/$language.json")), true);
            } else {
                $existingTranslations = [];
            }

            $missingTranslations = $this->missingTranslations($requiredTranslations, $existingTranslations);

            foreach ($missingTranslations as $key => $path) {
                $rows[] = [$language, $key, $path];
            }

            // sets translation values to empty string
            $missingTranslations = array_fill_keys(array_keys($missingTranslations), '');
            (new FileTranslations(resource_path("lang/$language.json")))->update($missingTranslations);
        }

        $this->table(["Language", "Key", "Path"], $rows);
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
