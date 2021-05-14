@extends('layouts.app')

@section('content')
    <div id="player"></div>
    @foreach ($savedTracks as $track)
        {{$track}}
    @endforeach
@endsection
