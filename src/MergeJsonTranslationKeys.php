<?php

namespace Druc\Langscanner;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class MergeJsonTranslationKeys
{
    private array $allTranslationKeys;

    public function merge()
    {
        $this->jsonLanguageFiles()->each(function ($file) {
            $translations = json_decode($file->getContents(), true);
            $translations = array_merge($this->allTranslationKeys(), $translations);

            (new FileTranslations($file->getRealPath()))->update($translations);
        });
    }

    private function jsonLanguageFiles(): Collection
    {
        return Collection::make(File::files(resource_path('lang')))
            ->filter(function ($file) {
                return $file->getExtension() === 'json';
            });
    }

    private function allTranslationKeys(): array
    {
        if (isset($this->allTranslationKeys)) {
            return $this->allTranslationKeys;
        }

        return $this->allTranslationKeys = $this->jsonLanguageFiles()->reduce(function ($carry, $file) {
            $translations = json_decode($file->getContents(), true);
            return array_merge($carry, array_fill_keys(array_keys($translations), ''));
        }, []);
    }
}
