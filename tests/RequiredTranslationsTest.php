<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\RequiredTranslations;
use Illuminate\Filesystem\Filesystem;

class RequiredTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_required_translations()
    {
        $requiredTranslations = new RequiredTranslations(
            new Filesystem,
            [__DIR__.'/fixtures/test-files'],
            [],
            ['__', 'trans', 'trans_choice', '@lang', 'Lang::get']
        );

        $this->assertEquals([
            'This will go in the JSON array' => '__.txt',
            'lang.first_match' => 'alt_lang.txt',
            'lang_get.first' => 'lang_get.txt',
            'lang_get.second' => 'lang_get.txt',
            'trans.first_match' => 'trans.txt',
            'trans' => 'trans.txt',
            'trans.third_match' => 'trans.txt',
            'trans_choice.with_params' => 'trans_choice.txt',
        ], $requiredTranslations->toArray());
    }
}
