<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DebugUserRoles extends Command
{
    protected $signature = 'debug:user-roles {--fix : Fix users with incorrect roles}';
    protected $description = 'Debug user roles to identify routing issues';

    public function handle()
    {
        $this->info('Checking user roles...');
        
        $users = User::all(['id', 'name', 'email', 'role']);
        
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            $users->map(function($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role ?? 'NULL'
                ];
            })->toArray()
        );
        
        $adminCount = $users->where('role', 'admin')->count();
        $userCount = $users->where('role', 'user')->count();
        $nullCount = $users->whereNull('role')->count();
        $invalidCount = $users->whereNotIn('role', ['admin', 'user', 'signatory'])->count();
        
        $this->info("Summary:");
        $this->info("- Admin users: {$adminCount}");
        $this->info("- Regular users: {$userCount}");
        $this->info("- Users with null role: {$nullCount}");
        $this->info("- Users with invalid role: {$invalidCount}");
        
        if ($nullCount > 0 || $invalidCount > 0) {
            $this->warn("Found {$nullCount} users with null roles and {$invalidCount} users with invalid roles.");
            
            if ($this->option('fix')) {
                $this->info("Fixing user roles...");
                $fixedCount = 0;
                
                foreach ($users as $user) {
                    if (empty($user->role) || !in_array($user->role, ['admin', 'user', 'signatory'])) {
                        $oldRole = $user->role;
                        $user->role = 'user';
                        $user->save();
                        $this->line("Fixed user {$user->email}: {$oldRole} -> user");
                        $fixedCount++;
                    }
                }
                
                $this->info("Fixed {$fixedCount} users.");
            } else {
                $this->info("Users with null roles:");
                $users->whereNull('role')->each(function($user) {
                    $this->line("- {$user->email} (ID: {$user->id})");
                });
                
                $this->info("Users with invalid roles:");
                $users->whereNotIn('role', ['admin', 'user', 'signatory'])->each(function($user) {
                    $this->line("- {$user->email} (ID: {$user->id}, Role: {$user->role})");
                });
                
                $this->info("Run with --fix option to automatically fix these users.");
            }
        }
        
        return 0;
    }
}
