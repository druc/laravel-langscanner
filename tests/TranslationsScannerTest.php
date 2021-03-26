<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\TranslationsScanner;

class TranslationsScannerTest extends TestCase
{
    /** @test */
    public function it_finds_all_translations()
    {
        $matches = app(TranslationsScanner::class)->translations();

        $this->assertEquals([
            'single' => [
                'single' => ['This will go in the JSON array' => '', 'trans' => ''],
            ],
            'group' => [
                'lang' => [
                    'first_match' => '',
                ],
                'lang_get' => ['first' => '', 'second' => ''],
                'trans' => ['first_match' => '', 'third_match' => ''],
                'trans_choice' => ['with_params' => ''],
            ],
        ], $matches);
        $this->assertCount(2, $matches);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('langscanner.scan_paths', [__DIR__ . '/fixtures/scan-tests']);
        $app['config']->set('langscanner.translation_methods', ['__', 'trans', 'trans_choice', '@lang', 'Lang::get']);
    }
}
