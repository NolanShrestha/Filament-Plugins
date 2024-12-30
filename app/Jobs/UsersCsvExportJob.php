<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Writer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Import the Log facade

class UsersCsvExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Ensure the directory exists
        $exportDirectory = storage_path('app/exports');
        if (!File::exists($exportDirectory)) {
            File::makeDirectory($exportDirectory, 0755, true);
        }

        // Define the file path
        $filePath = $exportDirectory . '/users.csv';

        // Write the CSV file
        try {
            $csv = Writer::createFromPath($filePath, 'w+');
            $csv->insertOne(['ID', 'Name', 'Email']);

            foreach ($this->users as $user) {
                $csv->insertOne([$user->id, $user->name, $user->email]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create CSV file: ' . $e->getMessage());
            throw $e;
        }
    }
}
