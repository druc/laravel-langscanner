<?php

namespace Druc\Langscanner;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Languages
{
    private array $languages;

    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    public static function fromPath(string $path, Filesystem $filesystem = null): self
    {
        $filesystem ??= resolve(Filesystem::class);
        $languages = Collection::make($filesystem->files($path))
            ->filter(fn ($file) => $file->getExtension() === 'json')
            ->map(fn ($file) => $file->getFilenameWithoutExtension())
            ->values()
            ->toArray();

        return new self($languages);
    }

    public function all(): array
    {
        return $this->languages;
    }
}
