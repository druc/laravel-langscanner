<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class RequiredLanguages
{
    private Filesystem $disk;
    private string $languagesPath;
    private array $excludedLanguages;

    public function __construct(Filesystem $disk, string $languagesPath, array $excludedLanguages)
    {
        $this->disk = $disk;
        $this->languagesPath = $languagesPath;
        $this->excludedLanguages = $excludedLanguages;
    }

    public function toArray(): array
    {
        return Collection::make($this->disk->files($this->languagesPath))
            ->filter(function ($file) {
                return $file->getExtension() === 'json';
            })->map(function ($file) {
                return $file->getFilenameWithoutExtension();
            })->filter(function ($lang) {
                return !in_array($lang, $this->excludedLanguages);
            })
            ->values()
            ->toArray();
    }
}
