<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class RequiredTranslations
{
    private Filesystem $disk;
    private array $paths;
    private array $translationMethods;
    private array $excludedPaths;
    private array $translations;

    public function __construct(Filesystem $disk, $options)
    {
        Assert::keyExists($options, 'paths');
        Assert::keyExists($options, 'excluded_paths');
        Assert::keyExists($options, 'translation_methods');

        $this->disk = $disk;
        $this->paths = $options['paths'];
        $this->excludedPaths = $options['excluded_paths'];
        $this->translationMethods = $options['translation_methods'];
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

        // exclude php translations
        $results = array_diff_key($results, $this->getPhpTranslations());

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

    private function getPhpTranslations(): array
    {
        return Collection::make($this->disk->allFiles(resource_path('lang')))
            ->filter(function ($file) {
                return $file->getExtension() === 'php';
            })->reduce(function ($carry, $file) {
                $translations = $this->disk->getRequire($file->getRealPath());

                return $carry->merge(Arr::dot([
                    $file->getFilenameWithoutExtension() => $translations
                ]));
            }, collect())->filter(function ($item) {
                return is_string($item);
            })->toArray();
    }
}
