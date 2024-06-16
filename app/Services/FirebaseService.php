<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;

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

    public function getGoogleOAuthToken()
    {
        return $this->accessToken;
    }
    public function sendNotification(array $deviceTokens, $title, $body, $googleOAuthToken)
    {
        $url ='https://fcm.googleapis.com/v1/projects/'. env('FIREBASE_PROJECT_ID') .'/messages:send';

        $headers = [
            'Authorization' => 'Bearer ' . $googleOAuthToken,
            'Content-Type' => 'application/json',
        ];

        $batchSize = 500;
        $batches = array_chunk($deviceTokens, $batchSize);

//        $message = [
//            'message' => [
//                'token' => $deviceTokens,
//                'notification' => [
//                    'title' => $title,
//                    'body' => $body,
//                ],
//            ],
//        ];

        $message = [
            'message' => [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ],
        ];

        foreach ($batches as $batch) {
            $message['message']['token'] = $batch;
            $response = $this->client->post($url, [
                'headers' => $headers,
                'body' => json_encode($message),
            ]);

            return response()->json(($response->getStatusCode() == 200) ? ['Success' => true] : ['Error' => 'Failed to send notification'], ($response->getStatusCode() == 200) ? 200 : 400);
        }

        $response = $this->client->post($url, [
                'headers' => $headers,
                'body' => json_encode($message),
        ]);

        dd($response);

        return $response;
    }
}
