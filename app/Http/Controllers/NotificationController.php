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

        $firebaseTokens = ['fYR-VUjMSXmJUIsUMJmwAT:APA91bEp5AEb70AmmHJxsIT56Ny8m9g9OZ5HWNSNRccI3SK213hFCOtyKG5Dj0mOZYByED6fOg_PsRUON5kNmuaU8KRAVUYyN66tPpZm_UM1JdoKa5WXBKbokgRNsTYByp_AQvqw5dOf', 'fG9NU2J3SZ-LYEH5Ic1LSs:APA91bEQELgTd_FpgMQIAJ3ZZazRZ7ay9YTEMbPIjc0m8lDUrCmTbpaP1rHUcSYYCeut-iav8bdvwKWFfiU2C1p10pxtfK0ZxGRIWOXAig_knMfemft_U-03kRrDfMa24MAQe87cYvbP', 'cQc56FW-Rp6KBFLx7m6EXt:APA91bEIL2_ZaOvnaJaf84OWTt3zYb5aiY-vhsNgxb9Z6dNnzG5NO45B0kLuPLC42qSu8NarBvtzTvOdGzd0VjHyddSanv2Rsv28YPsgI2BCydZCiLuogck5i-KHISXmIAJopb_A1_8S'];

        $googleOAuthToken = $this->firebaseService->getGoogleOAuthToken();

        $response = $this->firebaseService->sendNotification($firebaseTokens, $title, $body ,$googleOAuthToken);

        return $response->json($response);
    }
}
