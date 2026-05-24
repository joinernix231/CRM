<?php

namespace Tests\Concerns;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait AuthenticatesApiUsers
{
    protected function actingAsApi(User $user): static
    {
        Sanctum::actingAs($user);

        $token = $user->createToken('test')->plainTextToken;

        return $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->withHeader('Accept', 'application/json');
    }
}
