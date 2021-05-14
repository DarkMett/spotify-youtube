<?php

namespace App\Http\Controllers;

class YoutubeController extends Controller
{

    public function getVideosByName()
    {
        $apikey = env('GOOGLE_API_KEY');
        $name = urlencode($_GET['name']);

        $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $name . '&maxResults=20&key=' . $apikey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        $data = json_decode($response);
        $value = json_decode(json_encode($data), true);

        return json_encode($value['items']);
    }

    public function getVideoIdByName()
    {
        $apikey = env('GOOGLE_API_KEY');
        $name = urlencode($_GET['name']);
        $position = $_GET['position'] ?: 0;

        $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $name . '&maxResults=10&key=' . $apikey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        $data = json_decode($response);
        $value = json_decode(json_encode($data), true);

        return $value['items'][$position]['id']['videoId'];
    }
}