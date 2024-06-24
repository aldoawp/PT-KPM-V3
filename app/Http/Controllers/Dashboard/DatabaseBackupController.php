<?php

namespace App\Http\Controllers\Dashboard;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        if (!Storage::exists(storage_path('\db_backup'))) {
            Storage::makeDirectory('\db_backup');
        }

        return view('database.index', [
            'files' => File::allFiles(storage_path('\app\db_backup'))
        ]);
    }

    // Backup database is not working, and you need to enter manually in terminal with command php artisan backup:run.
    public function create()
    {
        // Lock all tables
        DB::unprepared('FLUSH TABLES WITH READ LOCK;');

        $command = 'cd ' . base_path() . ' && php artisan backup:run --only-db --disable-notifications';

        // To use this code, make sure apache user has permission to write to the storage directory
        // and shell exec feature turned on
        shell_exec($command);

        // Unlock all tables
        DB::unprepared('UNLOCK TABLES');

        return Redirect::route('backup.index')->with('success', 'Backup Database Berhasil!');
    }

    public function download(string $getFileName)
    {
        $path = storage_path('app\db_backup\\' . $getFileName);

        return response()->download($path);
    }

    public function delete(string $getFileName)
    {
        Storage::delete('db_backup/' . $getFileName);

        return Redirect::route('backup.index')->with('success', 'Database Deleted Successfully!');
    }
}
