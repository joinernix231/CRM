<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;

class VerifyApiToken
{
    /**
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $plainTextToken = $request->bearerToken();

        if ($plainTextToken === null) {
            return $this->unauthorized('Se requiere un token de autenticación.');
        }

        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if ($accessToken === null) {
            return $this->unauthorized('Token de autenticación inválido.');
        }

        if ($accessToken->expires_at !== null && $accessToken->expires_at->isPast()) {
            return $this->unauthorized('El token de autenticación ha expirado.');
        }

        $user = $accessToken->tokenable;

        if ($user === null) {
            return $this->unauthorized('Token de autenticación inválido.');
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        $user->withAccessToken($accessToken);
        $request->setUserResolver(fn () => $user);

        session([
            'user_id' => $user->id,
            'user' => $user,
        ]);

        return $next($request);
    }

    /**
     * @return JsonResponse
     */
    private function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}
