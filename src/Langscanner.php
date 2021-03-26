<?php

namespace Cdruc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Langscanner
{
    protected TranslationsScanner $scanner;
    private Filesystem $disk;
    private string $languageFilesPath;
    private array $excludedLanguages;

    public function __construct(Filesystem $disk, $languageFilesPath, $scanner, $excludedLanguages)
    {
        $this->disk = $disk;
        $this->languageFilesPath = $languageFilesPath;
        $this->scanner = $scanner;
        $this->excludedLanguages = $excludedLanguages;
    }

    /**
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function missingTranslations(): array
    {
        $missingTranslations = [];

        foreach ($this->languages() as $language => $name) {
            $missing = $this->missingTranslationsFor($language);

            if (! empty($missing)) {
                $missingTranslations[$language] = $missing;
            }
        }

        return $missingTranslations;
    }

    /**
     * Get all translations for a particular language.
     *
     * @param string $language
     * @return \Illuminate\Support\Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function translationsFor(string $language): Collection
    {
        return Collection::make([
            'group' => $this->groupTranslationsFor($language),
            'single' => $this->singleTranslationsFor($language),
        ]);
    }

    /**
     * Get all of the single translations for a given language.
     *
     * @param string $language
     * @return Collection
     */
    private function singleTranslationsFor(string $language): Collection
    {
        $files = new Collection($this->disk->allFiles($this->languageFilesPath));

        return $files->filter(function ($file) use ($language) {
            return strpos($file, "{$language}.json");
        })->flatMap(function ($file) {
            return [
                $this->prefix($file) => new Collection(json_decode($this->disk->get($file), true)),
            ];
        });
    }

    private function prefix($file, $default = 'single'): string
    {
        if (Str::contains($file->getPathname(), 'vendor')) {
            $vendor = Str::before(Str::after($file->getPathname(), 'vendor' . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

            return "{$vendor}::{$default}";
        }

        return $default;
    }

    /**
     * Get all of the group translations for a given language.
     *
     * @param string $language
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function groupTranslationsFor(string $language): Collection
    {
        return $this->groupFilesFor($language)
            ->mapWithKeys(function ($file) {
                return [
                    $this->prefix($file, $file->getBasename('.php')) => new Collection(Arr::dot($this->disk->getRequire($file->getPathname()))),
                ];
            });
    }

    /**
     * Get all the group files for a given language.
     *
     * @param string $language
     * @return Collection
     */
    private function groupFilesFor(string $language): Collection
    {
        $groups = new Collection($this->disk->allFiles("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}"));
        // namespaced files reside in the vendor directory so we'll grab these
        // the `getVendorGroupFileFor` method
        $groups = $groups->merge($this->vendorGroupFilesFor($language));

        return $groups;
    }

    /**
     * Get all the vendor group files for a given language.
     *
     * @param string $language
     * @return Collection
     */
    private function vendorGroupFilesFor(string $language): Collection
    {
        if (! $this->disk->exists("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor')) {
            return Collection::make([]);
        }

        $vendorGroups = [];
        foreach ($this->disk->directories("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor') as $vendor) {
            $vendor = Arr::last(explode(DIRECTORY_SEPARATOR, $vendor));
            if (! $this->disk->exists("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . "{$vendor}" . DIRECTORY_SEPARATOR . "{$language}")) {
                array_push($vendorGroups, []);
            } else {
                array_push($vendorGroups, $this->disk->allFiles("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . "{$vendor}" . DIRECTORY_SEPARATOR . "{$language}"));
            }
        }

        return new Collection(Arr::flatten($vendorGroups));
    }

    /**
     * Find all of the translations in the app without translation for a given language.
     *
     * @param string $language
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function missingTranslationsFor(string $language): array
    {
        return $this->arrayDiffAssocRecursive(
            $this->scanner->translations(),
            $this->translationsFor($language)
        );
    }

    /**
     * Get all languages from the application.
     *
     * @return Collection
     */
    private function languages(): Collection
    {
        // Parse the language files path for directories and json files
        $directories = Collection::make($this->disk->directories($this->languageFilesPath))
            ->mapWithKeys(function ($directory) {
                $language = basename($directory);

                return [$language => $language];
            })->filter(function ($language) {
                // at the moment, we're not supporting vendor specific translations
                return $language != 'vendor';
            });

        $files = Collection::make($this->disk->files($this->languageFilesPath))
            ->filter(function ($file) {
                return $file->getExtension() === 'json';
            })->mapWithKeys(function ($file) {
                $language = Str::replaceLast('.json', '', $file->getBasename());

                return [$language => $language];
            });

        $languages = $files->merge($directories);

        return $languages->except($this->excludedLanguages);
    }

    /**
     * @param $arrayOne
     * @param $arrayTwo
     * @return array
     */
    private function arrayDiffAssocRecursive($arrayOne, $arrayTwo): array
    {
        $difference = [];

        foreach ($arrayOne as $key => $value) {
            if (is_array($value) || $value instanceof Collection) {
                if (! isset($arrayTwo[$key])) {
                    $difference[$key] = $value;
                } elseif (! (is_array($arrayTwo[$key]) || $arrayTwo[$key] instanceof Collection)) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->arrayDiffAssocRecursive($value, $arrayTwo[$key]);
                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (! isset($arrayTwo[$key])) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
