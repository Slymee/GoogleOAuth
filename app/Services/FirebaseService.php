<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;

class FirebaseService
{
    protected $client;
    protected $googleClient;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->googleClient = new Google_Client();
        $this->googleClient->setAuthConfig(storage_path('app/' . env('FIREBASE_JSON_FILE')));
        $this->googleClient->setSubject(env('FIREBASE_CLIENT_EMAIL'));
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];
    }

    /**
     * @return mixed
     */
    public function getGoogleOAuthToken(): mixed
    {
        return $this->accessToken;
    }

    public function sendNotification(array $deviceTokens, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];

        $batchSize = 500;
        $batches = array_chunk($deviceTokens, $batchSize);

        $responses = [];

        foreach ($batches as $batch) {
            $message = [
                'message' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'token' => $batch,
                ],
            ];

            $promises = [];

            foreach ($batch as $token) {
                $message['message']['token'] = $token;
                $promises[] = $this->client->postAsync($url, [
                    'headers' => $headers,
                    'body' => json_encode($message),
                ]);
            }

            try {
                $results = Promise\settle($promises)->wait();
                foreach ($results as $result) {
                    if ($result['state'] === 'fulfilled') {
                        $responses[] = json_decode($result['value']->getBody()->getContents(), true);
                    } else {
                        $responses[] = ['error' => $result['reason']->getMessage()];
                    }
                }
            } catch (RequestException $e) {
                $responses[] = ['error' => $e->getMessage()];
            }
        }

        return $responses;
    }
}
