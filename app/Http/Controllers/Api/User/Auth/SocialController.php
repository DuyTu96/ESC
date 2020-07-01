<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Abraham\TwitterOAuth\TwitterOAuth;

class SocialController extends ApiController
{

    public function redirect($service)
    {
        if ($service != 'twitter') {
            return Socialite::with($service)->stateless()->redirect();
        } else {
            $tempId = Str::random(40);
            $connection = new TwitterOAuth(config('services.twitter.client_id'), config('services.twitter.client_secret'));
            $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => config('services.twitter.redirect') . '?user=' . $tempId));
            Cache::put($tempId, $requestToken['oauth_token_secret'], 1);
            $url = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));

            return redirect($url);
        }
    }

    public function callback(Request $request, $service)
    {
        if ($service != 'twitter') {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } else {
            $serviceUser = $this->handleTwitterCallBack($request);
        }
        if ($service != 'google') {
            $serviceUserId = isset($serviceUser->id) ? $serviceUser->id : $serviceUser->getId();
            $email = $serviceUserId . '@' . $service . '.com';
        } else {
            $email = $serviceUser->getEmail();
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'email' => $email,
                'qr_i_token' => Str::random(36),
                'qr_g_token' => Str::random(36),
                'is_authenticated' => DBConstant::AUTH_AUTHENTICATED,
                'password' => ''
            ]);
        }
        $token = $this->getGuard('user')->fromUser($user);

        return redirect(env('CLIENT_BASE_URL'). '/login/social?token=' . $token);
    }

    public function loginSocial(Request $request)
    {
        if ($request->data['token']) {
            $token = $request->data['token'];
            $data = [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $this->getGuard('user')->factory()->getTTL() * 60,
            ];

            return $this->sendSuccess($data, trans('response.success'));
        }
    }

    private function handleTwitterCallBack($request)
    {
        $connection = new TwitterOAuth(
                        config('services.twitter.client_id'),
                        config('services.twitter.client_secret'),
                        $request->oauth_token,
                        Cache::get($request->user)
                    );

        $accessToken = $connection->oauth(
                            "oauth/access_token",
                            [
                                "oauth_verifier" => $request->oauth_verifier,
                                "oauth_token" => $request->oauth_token
                            ]
                        );

        $connection = new TwitterOAuth(
                        config('services.twitter.client_id'),
                        config('services.twitter.client_secret'),
                        $accessToken['oauth_token'],
                        $accessToken['oauth_token_secret']
                    );
        $content = $connection->get("account/verify_credentials");

        return $content;
    }
}
