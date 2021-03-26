<?php

namespace Cdruc\Langscanner;

use Cdruc\Langscanner\Commands\LangscannerCommand;
use Illuminate\Filesystem\Filesystem;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LangscannerServiceProvider extends PackageServiceProvider
{
    /**
     * @param \Spatie\LaravelPackageTools\Package $package
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-langscanner')
            ->hasConfigFile()
            ->hasCommand(LangscannerCommand::class);
    }

    /**
     * @throws \Spatie\LaravelPackageTools\Exceptions\InvalidPackage
     */
    public function register(): LangscannerServiceProvider
    {
        parent::register();

        $this->app->singleton(TranslationsScanner::class, function () {
            $config = $this->app['config']['langscanner'];

            return new TranslationsScanner(new Filesystem, $config['scan_paths'], $config['scan_excluded_paths'], $config['translation_methods']);
        });

        $this->app->singleton(Langscanner::class, function () {
            return new Langscanner(
                new Filesystem(),
                $this->app['path.lang'],
                $this->app[TranslationsScanner::class],
                $this->app['config']['langscanner']['excluded_languages']
            );
        });

        return $this;
    }
}
