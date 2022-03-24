<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\MissingTranslations;
use Druc\Langscanner\RequiredTranslations;
use Mockery\MockInterface;

class MissingTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_missing_translations(): void
    {
        $requiredTranslations = $this->mock(RequiredTranslations::class, function (MockInterface $mock) {
            $mock->shouldReceive("all")->andReturn([
                'Existing translation' => 'app.blade.php',
                'Missing translation' => 'app.blade.php',
            ]);
        });

        $fileTranslations = $this->mock(FileTranslations::class, function (MockInterface $mock) {
            $mock->shouldReceive("contains")->with("Existing translation")->andReturn(true);
            $mock->shouldReceive("contains")->with("Missing translation")->andReturn(false);
        });

        $missingTranslations = (new MissingTranslations(
            $requiredTranslations,
            $fileTranslations
        ))->all();

        $this->assertArrayHasKey("Missing translation", $missingTranslations);
        $this->assertArrayNotHasKey("Existing translation", $missingTranslations);
    }
}
