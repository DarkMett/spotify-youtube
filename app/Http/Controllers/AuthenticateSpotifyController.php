<?php

namespace App\Http\Controllers;

class AuthenticateSpotifyController extends Controller
{
    public function spotifyLogin()
    {
        $session = new \SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $verifier = $session->generateCodeVerifier();
        $challenge = $session->generateCodeChallenge($verifier);
        $state = $session->generateState();

        $options = [
            'code_challenge' => $challenge,
            'scope' => [
                'playlist-read-private',
                'user-read-private',
                'user-library-read',
            ],
            'state' => $state,
        ];

        session(['spotify_state' => $state]);
        session(['spotify_verifier' => $verifier]);

        return redirect($session->getAuthorizeUrl($options));
    }

    public function spotifyCallback()
    {

        $session = new \SpotifyWebAPI\Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $_GET['state'];

        if ($state !== session('spotify_state')) {
            die('State mismatch');
        }

        $session->requestAccessToken($_GET['code'], session('spotify_verifier'));
        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        session(['spotify_access_token' => $accessToken]);
        session(['spotify_refresh_token' => $refreshToken]);

        return redirect('/');
    }
}