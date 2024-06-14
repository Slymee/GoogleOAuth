<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $firebaseService;
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendPushNotification(Request $request)
    {
        $title = $request->notice_title;
        $body = $request->notice_description;

        $firebaseTokens = ['dqIsjbBFQ-eB3rctO8sg69:APA91bF1qd50iSqbZQHgX9kDVP2s9ALkt6GDkPqPsgaJ4fGL8LvNqiBa-fANm_0KtDXRqbsT_ax7JljcOShXs4zQzsi76VVNtZiYJuGXlD-xlXaqhTXL8j6KOegGn5DHyfXt-7xKbQtM'];

        $response = $this->firebaseService->sendNotification($firebaseTokens, $title, $body);

        return $response->json($response);
    }
}
