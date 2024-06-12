<?php
declare(strict_types=1);

namespace App\Helpers;
use Firebase\JWT\JWT;

class FirebaseJWT
{
    /**
     * @param $firebaseToken
     * @return string
     */
    public static function generateGoogleOAuthToken($firebaseToken): string
    {
        $firebaseTokenDecoded = JWT::decode($firebaseToken, env('FIREBASE_PRIVATE_KEY'));
        dd($firebaseTokenDecoded);
        $uid = $firebaseTokenDecoded->uid;

        $privateKey = env('FIREBASE_PRIVATE_KEY');
        $clientEmail = env('FIREBASE_CLIENT_EMAIL');
        $projectId = env('FIREBASE_PROJECT_ID');

        $payload = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => time(),
            'exp' => time() + 3600, // 1 Hour Expiration
            'uid' => $uid,
        ];

        $oauthToken = JWT::encode($payload, $privateKey, 'HS256');
        return $oauthToken;
    }
}
