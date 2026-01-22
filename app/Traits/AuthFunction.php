<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait AuthFunction
{
    /**
     * Protected common function for checking user authenication
     */
    protected function checkAuthUser(array $validated): ?User
    {
        try {
            /** Attempt to login */
            $user = User::where('username', $validated['username'])->first();
            Log::debug('User Auth Check Data', ['user' => $user?->username]);
        } catch (\Throwable $e) {
            Log::error('Failed to check user', ['errors' => $e->getMessage()]);

            return null;
        }

        /** Check user with password */
        if (! Hash::check($validated['password'], $user?->password)) {
            return null;
        }

        Log::info('Username '.$validated['username'].' logging in', ['username' => $validated['username']]);

        return $user;
    }
}
