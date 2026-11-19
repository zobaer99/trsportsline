<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create-user';
    protected $description = 'Create or update a TR Sports Line dashboard login';

    public function handle(): int
    {
        $name = $this->ask('Admin name');
        $email = strtolower((string) $this->ask('Admin email'));
        $password = $this->secret('Admin password (8+ characters)');
        if (strlen((string) $password) < 8 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid email and a password with at least 8 characters.');
            return self::FAILURE;
        }
        $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password]);
        $this->info("Admin login ready for {$user->email}.");
        return self::SUCCESS;
    }
}
