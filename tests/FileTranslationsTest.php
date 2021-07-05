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

        $fileTranslations = new FileTranslations($path);
        $fileTranslations->update([
            "Missing translation" => "",
            "Missing translation2" => "this one has content",
        ]);

        $updatedTranslations = json_decode(file_get_contents($path), true);

        $this->assertEquals("", $updatedTranslations["Missing translation"]);
        $this->assertEquals("this one has content", $updatedTranslations["Missing translation2"]);

        // Restore original content
        file_put_contents($path, $originalContent);
    }
}
