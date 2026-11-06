<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserRole extends Command
{
    protected $signature = 'user:role {email} {role}';
    protected $description = 'Assign the user, staff, or admin role to an existing account';

    public function handle(): int
    {
        $role = strtolower((string) $this->argument('role'));
        if (! in_array($role, User::ROLES, true)) {
            $this->error('Role must be one of: '.implode(', ', User::ROLES).'.');
            return self::FAILURE;
        }

        $user = User::whereRaw('LOWER(email) = ?', [strtolower((string) $this->argument('email'))])->first();
        if (! $user) {
            $this->error('No account found with that email address.');
            return self::FAILURE;
        }

        if ($user->role === 'admin' && $role !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            $this->error('Cannot remove the only administrator. Promote another account first.');
            return self::FAILURE;
        }

        $user->role = $role;
        $user->save();
        $this->info("{$user->email} now has the {$role} role.");
        return self::SUCCESS;
    }
}
