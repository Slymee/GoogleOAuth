<?php
declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class FirebaseRefreshToken
{
    public static function refreshToken($refreshToken)
    {
        $clientId = env('FIREBASE_CLIENT_ID');
        $clientSecret = env('FIREBASE_CLIENT_SECRET');
        $redirectUri = env('FIREBASE_REDIRECT_URI');

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
        ]);

        $newToken = json_decode($response->body(), true);

        return $newToken['access_token'];
    }
}
