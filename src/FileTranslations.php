<?php

namespace Druc\Langscanner;

class FileTranslations
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function update(array $translations)
    {
        try {
            $existingTranslations = json_decode(file_get_contents($this->path), true);
        } catch (\Exception $e) {
            $existingTranslations = [];
        }

        $translations = array_merge($existingTranslations, $translations);
        $translations = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($this->path, $translations);
    }
}
