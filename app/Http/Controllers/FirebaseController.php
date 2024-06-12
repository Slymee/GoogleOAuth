<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\FirebaseJWT;
use App\Helpers\FirebaseRefreshToken;

class FirebaseController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generateToken(Request $request): JsonResponse
    {
        $firebaseToken = $request->input('message.token');
        $token = FirebaseJWT::generateGoogleOAuthToken($request->input('message.token'));
        return response()->json(['token' => $token]);
    }


//    public function refreshToken($refreshToken)
//    {
//        $newToken = FirebaseRefreshToken::refreshToken($refreshToken);
//        return response()->json(['token' => $newToken]);
//    }
}
