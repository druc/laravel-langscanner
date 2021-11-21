<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\Languages;
use Illuminate\Filesystem\Filesystem;

class LanguagesTest extends TestCase
{
    /** @test */
    public function it_gets_all_languages_in_path(): void
    {
        $disk = resolve(Filesystem::class);
        $disk->makeDirectory(__DIR__ . "/fixtures/lang/");
        $disk->put(__DIR__ . "/fixtures/lang/en.json", "");
        $disk->put(__DIR__ . "/fixtures/lang/es.json", "");

        $this->assertEquals(['en', 'es'], Languages::fromPath(__DIR__ . '/fixtures/lang')->all());

        $disk->deleteDirectory(__DIR__ . "/fixtures/lang/");
    }
}
