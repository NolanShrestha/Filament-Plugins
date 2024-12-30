<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Writer;

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
        $csv = Writer::createFromPath(storage_path('app/exports/users.csv'), 'w+');
        $csv->insertOne(['ID', 'Name', 'Email']);

        foreach ($this->users as $user) {
            $csv->insertOne([$user->id, $user->name, $user->email]);
        }

      
    }
}
