<?php

namespace Druc\Langscanner;

class FileTranslations
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function update(MissingTranslations $missingTranslations)
    {
        $existingTranslations = file_get_contents($this->path);
        $existingTranslations = json_decode($existingTranslations, true);

        // Sets keys to an empty string: ['needs translation' => '', etc]
        $unfilledTranslations = array_fill_keys(array_keys($missingTranslations->toArray()), '');

        $mergedTranslations = array_merge($existingTranslations, $unfilledTranslations);
        $mergedTranslations = json_encode($mergedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($this->path, $mergedTranslations);
    }
}
