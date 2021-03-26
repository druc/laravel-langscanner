<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\ExistingTranslations;
use Illuminate\Filesystem\Filesystem;

class ExistingTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_existing_translations()
    {
        $existingTranslations = new ExistingTranslations(
            new Filesystem,
            __DIR__.'/fixtures/lang',
            'en'
        );

        $this->assertEquals([
            "Hello" => "Hello",
            "What's up" => "What's up!"
        ], $existingTranslations->toArray());
    }
}
