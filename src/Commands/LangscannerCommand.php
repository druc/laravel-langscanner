<?php

namespace Druc\Langscanner\Commands;

use Druc\Langscanner\CachedFileTranslations;
use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\Languages;
use Druc\Langscanner\MissingTranslations;
use Druc\Langscanner\RequiredTranslations;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LangscannerCommand extends Command
{
    protected $signature = 'langscanner {language?}';
    protected $description = "Updates translation files with missing translation keys.";

    public function handle(Filesystem $filesystem): void
    {
        if ($this->argument('language')) {
            $languages = new Languages([$this->argument('language')]);
        } else {
            $languages = Languages::fromPath(config('langscanner.lang_dir_path'), $filesystem);
        }

        foreach ($languages->all() as $language) {
            $fileTranslations = new CachedFileTranslations(
                new FileTranslations(['language' => $language])
            );

            $missingTranslations = new MissingTranslations(
                new RequiredTranslations(config('langscanner')),
                $fileTranslations
            );

            $fileTranslations->update(
                // sets translation values to empty string
                array_fill_keys(
                    array_keys($missingTranslations->all()),
                    ''
                )
            );

            // Render table
            $this->comment(PHP_EOL);
            $this->comment(strtoupper($language) . " missing translations:");

            $rows = [];

            foreach ($missingTranslations->all() as $key => $path) {
                $rows[] = [$key, $path];
            }

            $this->table(["Key", "Path"], $rows);
        }
    }
}
