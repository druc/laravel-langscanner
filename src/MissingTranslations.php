<?php

namespace Druc\Langscanner;

class MissingTranslations
{
    private RequiredTranslations $requiredTranslations;
    private ExistingTranslations $existingTranslations;

    public function __construct(RequiredTranslations $requiredTranslations, ExistingTranslations $existingTranslations)
    {
        $this->requiredTranslations = $requiredTranslations;
        $this->existingTranslations = $existingTranslations;
    }

    public function toArray(): array
    {
        $requiredTranslations = $this->requiredTranslations->toArray();
        $existingTranslations = $this->existingTranslations->toArray();
        $missing = [];

        foreach ($requiredTranslations as $key => $value) {
            if (empty($existingTranslations[$key])) {
                $missing[$key] = $value;
            }
        }

        return $missing;
    }
}
