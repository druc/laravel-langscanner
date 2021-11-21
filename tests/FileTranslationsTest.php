<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\FileTranslations;
use Illuminate\Filesystem\Filesystem;

class FileTranslationsTest extends TestCase
{
    /** @test */
    public function it_finds_all_file_translations(): void
    {
        $translations = ['Existing translation' => 'Some Translation'];
        $this->putTranslations("en", $translations);

        $this->assertEquals($translations, $this->fileTranslations()->all());

        unlink($this->langPath("en"));
    }

    /** @test */
    public function it_checks_if_it_contains_translation_key(): void
    {
        $translations = ['Existing translation' => 'Some Translation'];
        $this->putTranslations("en", $translations);

        $fileTranslations = $this->fileTranslations();

        $this->assertTrue($fileTranslations->contains('Existing translation'));
        $this->assertFalse($fileTranslations->contains('Missing translation'));

        unlink($this->langPath('en'));
    }

    /** @test */
    public function it_updates_translations(): void
    {
        $translations = ['Existing translation' => 'Some Translation'];
        $this->putTranslations("en", $translations);

        $fileTranslations = $this->fileTranslations();

        $fileTranslations->update([
            "Missing translation" => "",
            "Missing translation2" => "this one has content",
        ]);

        $updatedTranslations = $fileTranslations->all();
        $this->assertEquals("", $updatedTranslations["Missing translation"]);
        $this->assertEquals("this one has content", $updatedTranslations["Missing translation2"]);

        unlink($this->langPath('en'));
    }

    private function putTranslations($language, array $translations): void
    {
        $disk = resolve(Filesystem::class);
        $disk->put($this->langPath($language), json_encode($translations, JSON_THROW_ON_ERROR));
    }

    private function langPath(string $filename = null): string
    {
        if ($filename !== null) {
            return __DIR__ . "/fixtures/$filename.json";
        }

        return __DIR__ . "/fixtures/";
    }

    private function fileTranslations(): FileTranslations
    {
        return (new FileTranslations([
            'language' => 'en',
            'rootPath' => $this->langPath(),
        ]));
    }
}
