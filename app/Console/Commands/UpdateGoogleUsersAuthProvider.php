<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateGoogleUsersAuthProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-google-auth-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing users with Google profile photos to have auth_provider set to google';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('profile_photo_path')
            ->where('profile_photo_path', 'like', 'http%')
            ->whereNull('auth_provider')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $user->auth_provider = 'google';
            $user->save();
            $count++;
        }

        $this->info("Updated {$count} users with Google auth provider.");
    }
}
