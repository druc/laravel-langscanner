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
    private string $langDirPath;

    public function __construct(array $options, Filesystem $disk = null)
    {
        Assert::keyExists($options, 'paths');
        Assert::keyExists($options, 'excluded_paths');
        Assert::keyExists($options, 'translation_methods');
        Assert::keyExists($options, 'lang_dir_path');

        $this->disk = $disk ?? resolve(Filesystem::class);
        $this->paths = $options['paths'];
        $this->excludedPaths = $options['excluded_paths'];
        $this->translationMethods = $options['translation_methods'];
        $this->langDirPath = $options['lang_dir_path'];
    }

    public function all(): array
    {
        if (isset($this->translations)) {
            return $this->translations;
        }

        $results = [];

        foreach ($this->files() as $file) {
            if (preg_match_all($this->pattern(), $file->getContents(), $matches)) {
                foreach ($matches[2] as $key) {
                    if (!empty($key)) {
                        $results[$key] = $file->getFilename();
                    }
                }
            }
        }

        // exclude php translations
        $results = array_diff_key($results, $this->existingPhpTranslations());

        return $this->translations = $results;
    }

    private function files(): array
    {
        $files = $this->disk->allFiles($this->paths);

        return Collection::make($files)
            ->filter(fn ($file) => !Str::startsWith($file->getPathName(), $this->excludedPaths))
            ->toArray();
    }

    private function existingPhpTranslations(): array
    {
        return Collection::make($this->disk->allFiles($this->langDirPath))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->reduce(function ($carry, $file) {
                $translations = $this->disk->getRequire($file->getRealPath());

                return $carry->merge(Arr::dot([
                    $file->getFilenameWithoutExtension() => $translations,
                ]));
            }, Collection::make([]))
            ->filter(fn ($item) => is_string($item))
            ->toArray();
    }

    private function pattern(): string
    {
        // See https://regex101.com/r/jS5fX0/5
        return
            "/" .
            "[^\w]" . // Must not start with any alphanum or _
            "(?<!->)" . // Must not start with ->
            '(' . implode('|', $this->translationMethods) . ')' .// Must start with one of the functions
            "\(" .// Match opening parentheses
            "[\r\n|\r|\n]*?" .// Ignore new lines
            "[\'\"]" .// Match " or '
            "(" .// Start a new group to match:
            ".*" .// Must start with group
            ")" .// Close group
            "[\'\"]" .// Closing quote
            "[\r\n|\r|\n]*?" .// Ignore new lines
            "[\),]" . // Close parentheses or new parameter
            "/siuU";
    }
}
