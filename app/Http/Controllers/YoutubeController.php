<?php

namespace App\Http\Controllers;

class YoutubeController extends Controller
{

    public function getVideosByName()
    {
        $apikey = env('GOOGLE_API_KEY');
        $name = urlencode($_GET['name']);

        $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $name . '&maxResults=3&key=' . $apikey;

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

        foreach ($value['items'] as $key => $item) {
            $deviation = $this->thumbSizeStandardDeviation($item['id']['videoId']);
            if ($deviation < 20) {
                if ($key === 0) {
                    return json_encode(['success' => false, 'action' => 'next']);
                } else {
                    unset($value['items'][$key]);
                }
            }
        }

        return json_encode(['success' => true, 'data' => $value['items']]);
    }

    protected function thumbSizeStandardDeviation($videoId)
    {
        $fileSizes = [];
        for($i = 1; $i <= 3; $i++) {
            $thumbnailUrl =
                "http://i.ytimg.com/vi/" . $videoId . "/" . $i . ".jpg";
            $fileSizes[] = $this->getRemoteFileSize($thumbnailUrl);
        }

        return $this->standardDeviation($fileSizes);
    }

    protected function getRemoteFileSize($url)
    {
        $headers = array_change_key_case(get_headers($url, TRUE));
        return $headers['content-length'];
    }

    protected function standardDeviation($numbers)
    {
        $mean = array_sum($numbers) / count($numbers);
        $differenceSum = 0;
        foreach($numbers as $number) {
            $differenceSum += pow($number - $mean, 2);
        }
        return sqrt($differenceSum / count($numbers));
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