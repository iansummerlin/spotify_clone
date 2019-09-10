var currentPlaylist = [],
    shufflePlaylist = [],
    pagePlaylist = [],
    audioElement,
    mouseDown = false,
    currentIndex = 0,
    repeat = false,
    shuffle = false,
    userLoggedIn,
    timer;

function openPage(url) {
    if (timer != null) clearTimeout(timer);

    if (url.indexOf("?") == -1) url = url + "?";

    var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    $('#mainContent').load(encodedUrl);
    $('body').scrollTop(0);
    history.pushState(null, null, url);
}

function createPlaylist() {
    var playlistName = prompt("Please enter the name of your playlist");

    if (playlistName != null) {
        $.post("includes/handlers/ajax/createPlaylist.php", { name: playlistName, username: userLoggedIn })
        .done(function(error) {
            if (error != "") {
                alert(error);
                return;
            }
            openPage("myMusic.php");
        });
    }
}

function formatTime(seconds) {
    var time = Math.round(seconds),
        minutes = Math.floor(time / 60),
        seconds = time - (minutes * 60),
        extraZero = (seconds < 10) ? "0" : "";

    return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

    var progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function playSongFromArtistPage() {
    setTrack(pagePlaylist[0], pagePlaylist, true);
}

function Audio() {
    this.currentlyPlaying;
    this.audio = document.createElement('audio');

    this.audio.addEventListener("ended", function() {
        nextSong();
    });

    this.audio.addEventListener("canplay", function() {
        var duration = formatTime(this.duration);
        $("span.progressTime.remaining").text(duration);
    });

    this.audio.addEventListener("timeupdate", function() {
        if (this.duration) updateTimeProgressBar(this);  
    });


    this.audio.addEventListener("volumechange", function() {
        updateVolumeProgressBar(this);
    });

    this.setTrack = (track) => {
        this.currentlyPlaying = track;
        this.audio.src = track.path;
    }

    this.play = () => {
        this.audio.play();
    }

    this.pause = () => {
        this.audio.pause();
    }

    this.setTime = (seconds) => {
        this.audio.currentTime = seconds;
    }

}
