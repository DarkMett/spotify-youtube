@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-3">
            <div class="mb-3 mt-3">
                <select class="form-select js-select-playlist" aria-label="Default select example">
                    <option value="favorite" selected>Любимые треки</option>
                    @foreach ($playlists as $playlist)
                        <option value="{{$playlist->id}}">{{$playlist->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="tracks-container mt-3">
                <div class="list-group">
                    @foreach ($tracks as $key => $track)
                        <button type="button" data-position="{{$key}}" class="list-group-item list-group-item-action js-select-track @if ($key === 0) active @endif">{{$track}}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-6 mt-3">
            <div class="video-container d-flex justify-content-center">
                <div id="player"></div>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-primary js-next-current-track">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"></path>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"></path>
                    </svg>
                    Другое видео
                </button>
                <button type="button" class="btn btn-primary js-next-track">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"></path>
                    </svg>
                    Следующй трек
                </button>
                <div class="form-switch">
                    <input class="form-check-input" name="random" type="checkbox" id="flexSwitchCheckRandom">
                    <label class="form-check-label" for="flexSwitchCheckRandom">Random</label>
                </div>
                <div class="form-switch">
                    <input class="form-check-input" name="live-mode" type="checkbox" id="flexSwitchCheckLive">
                    <label class="form-check-label" for="flexSwitchCheckLive">Live mode</label>
                </div>
            </div>
        </div>



    </div>
@endsection
