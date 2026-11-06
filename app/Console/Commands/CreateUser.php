<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    protected $signature = 'user:create';

    protected $description = 'Create a login account and assign its role';

    public function handle(): int
    {
        $data = [
            'name' => trim((string) $this->ask('Full name')),
            'email' => Str::lower(trim((string) $this->ask('Email address'))),
            'role' => (string) $this->choice('Role', User::ROLES, 1),
            'password' => (string) $this->secret('Password'),
            'password_confirmation' => (string) $this->secret('Confirm password'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:'.implode(',', User::ROLES)],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create($data);

        $this->info('Login account created with role: '.$data['role'].'.');

        return self::SUCCESS;
    }
}
