<?php

namespace Druc\Langscanner;

class MissingTranslations
{
    private array $requiredTranslations;
    private array $existingTranslations;

    public function __construct(array $requiredTranslations, array $existingTranslations)
    {
        $this->requiredTranslations = $requiredTranslations;
        $this->existingTranslations = $existingTranslations;
    }

    public function toArray(): array
    {
        $missing = [];

        foreach ($this->requiredTranslations as $key => $value) {
            if (empty($this->existingTranslations[$key])) {
                $missing[$key] = $value;
            }
        }

        return $missing;
    }
}
