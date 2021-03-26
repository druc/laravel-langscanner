<?php

namespace Cdruc\Langscanner\Commands;

use Cdruc\Langscanner\Langscanner;
use Illuminate\Console\Command;

class LangscannerCommand extends Command
{
    protected Langscanner $langscanner;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langscanner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all translation keys which don\'t have a corresponding translation';

    public function __construct(Langscanner $langscanner)
    {
        parent::__construct();
        $this->langscanner = $langscanner;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $missingTranslations = $this->langscanner->missingTranslations();

        if (empty($missingTranslations)) {
            return $this->info("No missing translations were detected.");
        }

        $rows = [];

        // set some headers for the table of results
        $headers = ["Language", "Type", "Group", "Key"];

        // iterate over each of the missing languages
        foreach ($missingTranslations as $language => $types) {
            // iterate over each of the file types (json or array)
            foreach ($types as $type => $keys) {
                // iterate over each of the keys
                foreach ($keys as $key => $value) {
                    // populate the array with the relevant data to fill the table
                    foreach ($value as $k => $v) {
                        $rows[] = [$language, $type, $key, $k];
                    }
                }
            }
        }

        // render the table of results
        $this->table($headers, $rows);
    }
}
