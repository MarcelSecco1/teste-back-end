<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $user->balance()->create([
            'amount' => 0.0,
        ]);
    }

    public function deleted(User $user): void
    {
        $user->balance()->delete();
    }
}
