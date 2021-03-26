<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class RequiredTranslations
{
    private Filesystem $disk;
    private array $paths;
    private array $translationMethods;
    private array $excludedPaths;
    private array $translations;

    public function __construct(
        Filesystem $disk,
        array $paths,
        array $excludedPaths,
        array $translationMethods
    ) {
        $this->disk = $disk;
        $this->paths = $paths;
        $this->excludedPaths = $excludedPaths;
        $this->translationMethods = $translationMethods;
    }

    public function toArray(): array
    {
        if (isset($this->translations)) {
            return $this->translations;
        }

        $pattern =
            // See https://regex101.com/r/jS5fX0/5
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $this->translationMethods).')'.// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\'\"]".// Match " or '
            '('.// Start a new group to match:
            '.*'.// Must start with group
            ')'.// Close group
            "[\'\"]".// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;

        $results = [];
        foreach ($this->files() as $file) {
            if (preg_match_all("/$pattern/siuU", $file->getContents(), $matches)) {
                foreach ($matches[2] as $key) {
                    if (!empty($key)) {
                        $results[$key] = $file->getFilename();
                    }
                }
            }
        }

        return $this->translations = $results;
    }

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
