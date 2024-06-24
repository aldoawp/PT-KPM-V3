<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RmOldBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rm-old-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old backup files older than 3 months.';

    private function getDate($fileName)
    {
        $fileName = explode('_', $fileName);
        $fileName =
            preg_replace('/.zip/', '', last($fileName));
        $fileName = explode('-', $fileName);

        return Carbon::parse($fileName[0] . '-' . $fileName[1] . '-' . $fileName[2] . ' ' . $fileName[3] . ':' . $fileName[4] . ':' . $fileName[5]);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running remove old backup files command at ' . now());

        if (Storage::exists('\db_backup')) {
            $currDate = Carbon::now();
            $files = Storage::allFiles('\db_backup');

            foreach ($files as $file) {
                $fileDate = $this->getDate($file);

                if ($fileDate->diffInMonths($currDate) > 3) {
                    Storage::delete($file);
                }
            }
        }
    }
}
