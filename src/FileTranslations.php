<?php

namespace Druc\Langscanner;

class FileTranslations
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function update(array $missingTranslations)
    {
        try {
            $existingTranslations = json_decode(file_get_contents($this->path), true);
        } catch (\Exception $e) {
            $existingTranslations = [];
        }

        $mergedTranslations = array_merge($existingTranslations, $missingTranslations);
        $mergedTranslations = json_encode($mergedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($this->path, $mergedTranslations);
    }
}
