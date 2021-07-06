<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\Languages;
use Illuminate\Filesystem\Filesystem;

class LanguagesTest extends TestCase
{
    /** @test */
    public function it_finds_all_required_languages()
    {
        $requiredLanguages = new Languages(
            new Filesystem,
            __DIR__.'/fixtures/lang',
            ['en']
        );

        $this->assertEquals(['es'], $requiredLanguages->toArray());
    }
}
