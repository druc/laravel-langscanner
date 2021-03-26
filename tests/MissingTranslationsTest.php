<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\ExistingTranslations;
use Druc\Langscanner\MissingTranslations;
use Druc\Langscanner\RequiredTranslations;

class MissingTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_missing_translations()
    {
        $requiredTranslations = $this->createMock(RequiredTranslations::class);
        $requiredTranslations->method('toArray')
            ->willReturn([
                "This will go in the JSON array" => "__.txt",
                "lang.first_match" => "alt_lang.txt",
            ]);

        $existingTranslations = $this->createMock(ExistingTranslations::class);
        $existingTranslations->method("toArray")
            ->willReturn([
                "lang.first_match" => "This translation exists so...",
            ]);

        $missingTranslations = new MissingTranslations($requiredTranslations, $existingTranslations);

        $this->assertEquals([
            "This will go in the JSON array" => "__.txt",
        ], $missingTranslations->toArray());
    }
}
