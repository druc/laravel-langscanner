<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\MissingTranslations;

class MissingTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_missing_translations()
    {
        $missingTranslations = new MissingTranslations([
            "This will go in the JSON array" => "__.txt",
            "lang.first_match" => "alt_lang.txt",
        ], [
            "lang.first_match" => "This translation exists so...",
        ]);

        $this->assertEquals([
            "This will go in the JSON array" => "__.txt",
        ], $missingTranslations->toArray());
    }
}
