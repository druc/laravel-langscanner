<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\FileTranslations;
use Druc\Langscanner\MissingTranslations;

class FileTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_existing_translations()
    {
        $path = __DIR__.'/fixtures/lang/es.json';
        $originalContent = file_get_contents($path);

        $missingTranslations = $this->createMock(MissingTranslations::class);
        $missingTranslations->method('toArray')
            ->willReturn([
                "Missing translation" => "app.blade.php"
            ]);

        $fileTranslations = new FileTranslations($path);
        $fileTranslations->update($missingTranslations);

        $updatedTranslations = json_decode(file_get_contents($path), true);

        $this->assertEquals("", $updatedTranslations["Missing translation"]);

        // Restore original content
        file_put_contents($path, $originalContent);
    }
}
