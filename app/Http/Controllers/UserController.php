<?php

namespace App\Http\Controllers;

use App\Jobs\UsersCsvExportJob;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Export the list of users as a CSV.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportUsers(Request $request)
    {
       
        $users = User::all();


        UsersCsvExportJob::dispatch($users);

       
        Notification::make()
            ->title('Export Started')
            ->body('The user export job is now running.')
            ->success()
            ->send();

        return response()->json(['message' => 'Export job has been dispatched.']);
    }
}
