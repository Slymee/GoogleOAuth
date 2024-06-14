<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class FirebaseService
{
    protected $client;
    protected $googleClient;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->googleClient = new Google_Client();
        $this->googleClient->setAuthConfig(storage_path('app/'. env('FIREBASE_JSON_FILE')));
        $this->googleClient->setSubject(env('FIREBASE_CLIENT_EMAIL'));
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];
    }

    public function sendNotification(array $deviceTokens, $title, $body): JsonResponse
    {
        $url ='https://fcm.googleapis.com/v1/projects/'. env('FIREBASE_PROJECT_ID') .'/messages:send';
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];

        $message = [
            'message' => [
                'token' => $deviceTokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ],
        ];


        $response = $this->client->post($url, [
            'headers' => $headers,
            'json' => json_encode($message),
        ]);

        return response()->json($response);
    }
}
