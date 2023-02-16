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
        try {
            $this->api->setAccessToken(session('spotify_access_token'));
            $data = [
                'playlists' => $this->getPlaylists(),
                'tracks' => $this->getAllTracksFormatted(),
            ];
            return view('main')->with($data);
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                session(['spotify_access_token' => null]);
                return redirect('/');
            }
        }

    }

    public function getPlaylistTracks($playlistId)
    {
        $this->api->setAccessToken(session('spotify_access_token'));
        $tracks = $this->api->getPlaylistTracks($playlistId);
    }

    public function getAllPlaylistsTracks($playlistId): array
    {

        $this->api->setAccessToken(session('spotify_access_token'));
        $tracks = $this->api->getPlaylistTracks($playlistId, ['limit' => $this->tracksLimit, 'offset' => $this->tracksOffset]);
        foreach ($tracks->items as $item) {
            $this->allTracks[] = $item->track;
        }

        $this->tracksOffset += $this->tracksLimit;

        if ($tracks->total > $this->tracksOffset) {
            return $this->getAllPlaylistsTracks($playlistId);
        } else {
            return $this->allTracks;
        }
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

    public function getPlaylistTracksFormatted($playlistId): array
    {
        $formattedTracks = session('spotify_playlist_' . $playlistId);
        if (!$formattedTracks) {

            $formattedTracks = [];
            $tracks = $this->getAllPlaylistsTracks($playlistId);
            foreach ($tracks as $track) {
                $formattedTracks[] = $this->getTrackString($track);
            }
            session(['spotify_playlist_' . $playlistId => $formattedTracks]);
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