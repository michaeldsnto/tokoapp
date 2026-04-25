<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin {name} {email} {password}', function (string $name, string $email, string $password) {
    $user = User::query()->updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'role' => 'admin',
            'password' => $password,
        ],
    );

    $this->info("Admin user ready: {$user->email}");
})->purpose('Create or update the first production admin user');
