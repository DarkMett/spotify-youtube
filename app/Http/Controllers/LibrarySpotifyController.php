<?php

namespace App\Http\Controllers;

use http\Exception\UnexpectedValueException;

class LibrarySpotifyController extends Controller
{

    private \SpotifyWebAPI\SpotifyWebAPI $api;
    private array $allTracks = [];
    private int $tracksLimit = 50;
    private int $tracksOffset = 0;

    public function __construct()
    {
        $this->api = new \SpotifyWebAPI\SpotifyWebAPI();
    }

    public function view()
    {
        $this->api->setAccessToken(session('spotify_access_token'));
        $data = [
            'playlists' => $this->getPlaylists(),
        ];
        return view('main')->with($data);
    }

    public function getAllSavedTracks(): array
    {

        $this->api->setAccessToken(session('spotify_access_token'));
        $tracks = $this->api->getMySavedTracks(['limit' => $this->tracksLimit, 'offset' => $this->tracksOffset]);
        foreach ($tracks->items as $item) {
            $this->allTracks[] = $item->track;
        }

        $this->tracksOffset += $this->tracksLimit;

        if ($tracks->total > $this->tracksOffset) {
            return $this->getAllSavedTracks();
        } else {
            return $this->allTracks;
        }
    }

    public function getPlaylists()
    {
        $this->api->setAccessToken(session('spotify_access_token'));
        $playlists = $this->api->getMyPlaylists();
        return $playlists->items;
    }

    public function getAllTracksFormatted(): array
    {
        $formattedTracks = session('spotify_fav_tracks');
        if (!$formattedTracks) {

            $formattedTracks = [];
            $tracks = $this->getAllSavedTracks();
            foreach ($tracks as $track) {
                $formattedTracks[] = $this->getTrackString($track);
            }
            session(['spotify_fav_tracks' => $formattedTracks]);
        }

        return $formattedTracks;
    }

    public function getTracksJson()
    {
        return json_encode($this->getAllTracksFormatted());
    }

    /**
     * @param $track
     * @return string
     */
    public function getTrackString($track): string
    {
        $artists = [];
        foreach ($track->artists as $artist) {
            $artists[] = $artist->name;
        }
        $trackString = implode(', ', $artists);
        $trackString .= ' - ' . $track->name;

        return $trackString;
    }
}