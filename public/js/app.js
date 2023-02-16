var player;
var tracks;
var videos;
var currentVideoPosition = 0;
var currentTrackPosition = 0;

$(document).on('click', '.js-next-current-track', function () {
    playNextCurrentVideo();
});

$(document).on('click', '.js-next-track', function () {
    playNextTrack();
})

$(document).on('click', '.js-select-track', function () {
    playTrack($(this).text());
    $('.js-select-track').removeClass('active');
    $(this).addClass('active');
    currentTrackPosition = parseInt($(this).data('position'));
})

$(document).on('change', '.js-select-playlist', function () {
    let playlistId = $(this).val();
    $.ajax({
        url: '/playlists/' + playlistId,
        type: 'GET',
        async: false,
        dataType: 'json',
        success: function (data) {
            $('.tracks-container .list-group').html('');
            data.forEach((track, index) => {
                $('.tracks-container .list-group').append(`<button type="button" data-position="${index}" class="list-group-item list-group-item-action js-select-track">${track}</button>`);
            });
            currentTrackPosition = 0;
        }
    });
})

function getCurrentTrackName()
{
    return $(`.tracks-container button[data-position=${currentTrackPosition}]`).text();
}

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}

function getNextTrackName()
{
    if ($('[name=random]').prop('checked')) {
        let tracksCount = $('.tracks-container button').length;
        currentTrackPosition = getRandomInt(tracksCount - 1);
    } else {
        currentTrackPosition++;
    }

    return getCurrentTrackName();
}

function onYouTubeIframeAPIReady()
{
    if ($('#player').length < 1) return;

    tracks = getSpotifyTracks();
    window.currentTrackName = getCurrentTrackName();
    let getVideo = getVideosByName(window.currentTrackName);
    videos = getVideo.data;
    let videoId = videos[currentVideoPosition].id.videoId;

    player = new YT.Player('player', {
        width: '100%',
        height: '100%',
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
        data: {name: name, position: currentVideoPosition},
        success: function (data) {
            id = data;
        }
    });

    return id;
}

function playTrack(name)
{
    $('.js-select-track').removeClass('active');
    $(`.js-select-track[data-position=${currentTrackPosition}]`).addClass('active');
    $(`.js-select-track[data-position=${currentTrackPosition}]`)[0].scrollIntoView();

    let getVideo = getVideosByName(name);
    if (getVideo.success) {
        videos = getVideo.data;
        let videoId = videos[currentVideoPosition].id.videoId;
        player.loadVideoById(videoId);
    } else if (getVideo.action === 'next') {
        playNextTrack();
    }
}

function onPlayerStateChange(event)
{
    if (event.data == YT.PlayerState.ENDED) {
        playNextTrack();
    }
}

function playNextTrack()
{
    currentVideoPosition = 0;
    let track = getNextTrackName();
    playTrack(track);
}

function playNextCurrentVideo()
{
    currentVideoPosition++;
    let videoId = videos[currentVideoPosition].id.videoId;
    player.loadVideoById(videoId);
}
