<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\RequiredLanguages;
use Illuminate\Filesystem\Filesystem;

class RequiredLanguagesTest extends TestCase
{
    /** @test */
    public function it_finds_all_required_languages()
    {
        $requiredLanguages = new RequiredLanguages(
            new Filesystem,
            __DIR__.'/fixtures/lang',
            ['en']
        );

        $this->assertEquals(['es'], $requiredLanguages->toArray());
    }
}
