<?php

namespace App\Services;

use App\Models\User;

class ClientDataServices
{
    public function getUserData(int $userId): ?User {
        return User::findOrFail($userId);
    }

    public function getUserByEmail(string $email): ?User {
        return User::where('email', $email)->first();
    }
}
