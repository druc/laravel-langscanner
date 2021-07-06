<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Languages
{
    private Filesystem $disk;
    private array $excludedLanguages;
    private string $path;

    public function __construct(Filesystem $disk, string $path, array $excludedLanguages)
    {
        $this->disk = $disk;
        $this->path = $path;
        $this->excludedLanguages = $excludedLanguages;
    }

    public function toArray(): array
    {
        return Collection::make($this->disk->files($this->path))
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
