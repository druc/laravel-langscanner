<?php

namespace Cdruc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class TranslationsScanner
{
    private Filesystem $disk;
    private array $paths;
    private array $translationMethods;
    private array $excludedPaths;
    private array $translations;

    /**
     * TranslationsScanner constructor.
     * @param \Illuminate\Filesystem\Filesystem $disk
     * @param array $paths
     * @param array $excludedPaths
     * @param array $translationMethods
     */
    public function __construct(Filesystem $disk, array $paths, array $excludedPaths, array $translationMethods)
    {
        $this->disk = $disk;
        $this->paths = $paths;
        $this->excludedPaths = $excludedPaths;
        $this->translationMethods = $translationMethods;
    }

    /**
     * Scan all the files in the provided $scanPath for translations.
     *
     * @return array
     */
    public function translations(): array
    {
        if (isset($this->translations)) {
            return $this->translations;
        }

        $results = ['single' => [], 'group' => []];

        // This has been derived from a combination of the following:
        // * Laravel Language Manager GUI from Mohamed Said (https://github.com/themsaid/laravel-langman-gui)
        // * Laravel 5 Translation Manager from Barry vd. Heuvel (https://github.com/barryvdh/laravel-translation-manager)
        $matchingPattern =
            // See https://regex101.com/r/jS5fX0/5
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '(' . implode('|', $this->translationMethods) . ')' .// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\'\"]".// Match " or '
            '('.// Start a new group to match:
            '.+'.// Must start with group
            ')'.// Close group
            "[\'\"]".// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;

        foreach ($this->files() as $file) {
            if (preg_match_all("/$matchingPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches[2] as $key) {
                    if (preg_match("/(^[a-zA-Z0-9:_-]+([.][^\1)\ ]+)+$)/siU", $key, $arrayMatches)) {
                        [$file, $k] = explode('.', $arrayMatches[0], 2);
                        $results['group'][$file][$k] = '';

                        continue;
                    } else {
                        $results['single']['single'][$key] = '';
                    }
                }
            }
        }

        return $this->translations = $results;
    }

    /**
     * @return array
     */
    private function files(): array
    {
        $files = $this->disk->allFiles($this->paths);

        foreach ($files as $i => $file) {
            foreach ($this->excludedPaths as $path) {
                if (Str::startsWith($file->getPathName(), $path)) {
                    unset($files[$i]);
                }
            }
        }

        return $files;
    }
}
