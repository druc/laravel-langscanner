<?php

namespace Druc\Langscanner;

use Druc\Langscanner\Contracts\FileTranslations;

class CachedFileTranslations implements FileTranslations
{
    private FileTranslations $fileTranslations;

    public function __construct(FileTranslations $fileTranslations)
    {
        $this->fileTranslations = $fileTranslations;
    }

    public function update(array $translations): void
    {
        $this->fileTranslations->update($translations);
    }

    public function all(): array
    {
        $this->fileTranslations->all();
    }

    public function contains(string $key): bool
    {
        return $this->fileTranslations->contains($key);
    }

    public function language(): string
    {
        return $this->fileTranslations->language();
    }
}
