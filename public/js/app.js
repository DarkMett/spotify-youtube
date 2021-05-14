var player;
var tracks;
var videos;
var currentTrackPosition = 0;

$(document).on('click', '.js-next-current-track', function () {
    playNextCurrentVideo();
});

$(document).on('click', '.js-next-track', function () {
    playNextTrack();
})

function onYouTubeIframeAPIReady()
{
    if ($('#player').length < 1) return;

    tracks = getSpotifyTracks();
    window.currentTrackName = getRandomItem(tracks);
    videos = getVideosByName(window.currentTrackName);
    let videoId = videos[currentTrackPosition].id.videoId;

    player = new YT.Player('player', {
        width: '100%',
        height: '560px',
        videoId: videoId,
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        },
        playerVars: {
            'origin': 'https://spotify-youtube.stepanov.w.ibrush.ru/'
        },
    });
}

function getSpotifyTracks()
{
    let result;
    $.ajax({
        url: '/tracks',
        type: 'GET',
        async: false,
        dataType: 'json',
        success: function (data) {
            result = data;
        }
    });

    return result;
}

function getRandomItem(items)
{
    return items[Math.floor(Math.random() * items.length)];
}

function onPlayerReady(event)
{
    event.target.playVideo();
}

function getVideosByName(name)
{

    let trackName = name;
    if ($('[name=live-mode]').prop('checked')) {
        trackName += ' live';
    }

    $.ajax({
        url: '/getVideosByName',
        type: 'GET',
        async: false,
        dataType: 'json',
        data: {name: trackName},
        success: function (data) {
            videos = data;
        }
    });

    return videos;
}

function getVideoIdByName(name)
{
    let id;
    $.ajax({
        url: '/getVideoIdByName',
        type: 'GET',
        async: false,
        data: {name: name, position: currentTrackPosition},
        success: function (data) {
            id = data;
        }
    });

    return id;
}

function onPlayerStateChange(event)
{
    if (event.data == YT.PlayerState.ENDED) {
        playNextTrack();
    }
}

function playNextTrack()
{
    currentTrackPosition = 0;
    let randomTrack = getRandomItem(tracks);
    videos = getVideosByName(randomTrack);
    let videoId = videos[currentTrackPosition].id.videoId;
    player.loadVideoById(videoId);
}

function playNextCurrentVideo()
{
    currentTrackPosition++;
    let videoId = videos[currentTrackPosition].id.videoId;
    player.loadVideoById(videoId);
}
