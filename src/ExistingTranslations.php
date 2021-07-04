<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class ExistingTranslations
{
    private Filesystem $disk;
    private string $language;
    private string $languagesPath;

    public function  __construct(Filesystem $disk, string $languagesPath, string $language)
    {
        $this->disk = $disk;
        $this->language = $language;
        $this->languagesPath = $languagesPath;
    }

    public function toArray(): array
    {
        $translationFiles = new Collection($this->disk->allFiles($this->languagesPath));

        return $translationFiles->filter(function ($file) {
            return strpos($file, "{$this->language}.json");
        })->flatMap(function ($file) {
            return json_decode($this->disk->get($file), true);
        })->toArray();
    }
}
