<?php 
    $songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

    $resultArray = [];
    while ($row = mysqli_fetch_array($songQuery)) {
        array_push($resultArray, $row['id']);
    }

    $jsonArray = json_encode($resultArray);
?>

<script>
    $(document).ready(() => {
        var newPlaylist = <?php echo $jsonArray; ?>;
        audioElement = new Audio();
        setTrack(newPlaylist[0], newPlaylist, false);
        updateVolumeProgressBar(audioElement.audio);

        $("#nowPlayingContainer").on("mousedown touchstart mousemove touchmove", function(e) {
            e.preventDefault();
        });

        $(".playbackBar .progressBar").mousedown(() => {
            mouseDown = true;
        });

        $(".playbackBar .progressBar").mousemove(function(e) {
            if(mouseDown) {
                timeFromOffset(e, this);
            }
        });

        $(".playbackBar .progressBar").mouseup(function(e) {
            timeFromOffset(e, this);
        });

        $(".volumeBar .progressBar").mousedown(() => {
            mouseDown = true;
        });

        $(".volumeBar .progressBar").mousemove(function(e) {
            if(mouseDown) {
                var percentage = e.offsetX / $(this).width();
                audioElement.audio.volume = percentage;
            }
        });

        $(".volumeBar .progressBar").mouseup(function(e) {
            var percentage = e.offsetX / $(this).width();
            audioElement.audio.volume = percentage;
        });

        $(document).mouseup(() => {
            mouseDown = false;
        });

    });
    
    function timeFromOffset(mouse, progressBar) {
        var percentage = mouse.offsetX / $(progressBar).width() * 100;
        var seconds = audioElement.audio.duration * percentage / 100;
        audioElement.setTime(seconds);
    }

    function prevSong() {
        if (audioElement.audio.currentTime >= 3 || currentIndex == 0) {
            audioElement.setTime(0);
        } else {
            currentIndex--;
            setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
        }
    }

    function nextSong() {
        if (repeat) { 
            audioElement.setTime(0); 
            playSong();
            return;
        }

        (currentIndex == currentPlaylist.length - 1) ? currentIndex = 0 : currentIndex++;

        var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
        setTrack(trackToPlay, currentPlaylist, true);
    }

    function setRepeat() {
        repeat = !repeat;
        var imageName = repeat ? "repeat-active.png" : "repeat.png";
        $(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
    }

    function setMute() {
        audioElement.audio.muted = !audioElement.audio.muted;
        var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
        $(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
    }

    function setShuffle() {
        shuffle = !shuffle;
        var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
        $(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);

        if (shuffle) {
            // Randomise playlist
            shuffleArray(shufflePlaylist);
            currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
        } else {
            // Go back to regular playlist
            currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
        }
    }
    
    function shuffleArray(a) {
        var j, x, i;
        for (i = a.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1));
            x = a[i];
            a[i] = a[j];
            a[j] = x;
        }
        return a;
    }

    function setTrack(trackId, newPlaylist, play) {

        if (newPlaylist != currentPlaylist) {
            currentPlaylist = newPlaylist;
            shufflePlaylist = currentPlaylist.slice();
            shuffleArray(shufflePlaylist);
        }

        shuffle ? currentIndex = shufflePlaylist.indexOf(trackId) : currentPlaylist.indexOf(trackId);

        pauseSong();

        $.post("includes/handlers/ajax/getSong.json.php", { songId: trackId }, (data) => {

            var track = JSON.parse(data);

            $(".trackInfo .trackName span").text(track.title);

            $.post("includes/handlers/ajax/getArtist.json.php", { artistId: track.artist }, (data) => {
                var artist = JSON.parse(data);
                $(".trackInfo .artistName span")
                    .text(artist.name)
                    .attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
            });

            $.post("includes/handlers/ajax/getArtwork.json.php", { albumId: track.album }, (data) => {
                var album = JSON.parse(data);
                $(".content .albumLink img")
                    .attr("src", album.artworkPath)
                    .attr("onclick", "openPage('album.php?id=" + album.id + "')");
                $(".trackInfo .trackName span").attr("onclick", "openPage('album.php?id=" + album.id + "')");
            });

            audioElement.setTrack(track);

            if (play) {
                playSong();
            }
        });
    }

    function playSong() {
        if (audioElement.audio.currentTime == 0) {
            $.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });
        }

        $(".controlButton.play").hide();
        $(".controlButton.pause").show();
        audioElement.play();
    }

    function pauseSong() {
        $(".controlButton.pause").hide();
        $(".controlButton.play").show();
        audioElement.pause();
    }
</script>

<div id="nowPlayingContainer">
    <div id="nowPlayingBar">

        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img src="" alt="album artwork" role="link" tabindex="0" class="albumArtwork">
                </span>
                <span class="trackInfo">
                    <span class="trackName">
                        <span role="link" tabindex="0"></span>
                    </span>
                    <span class="artistName">
                        <span role="link" tabindex="0"></span>
                    </span>
                </span>
            </div>
        </div>

        <div id="nowPlayingCentre">
            <div class="content playerControls">
                <div class="buttons">
                    <button class="controlButton shuffle" title="Shuffle music" onclick="setShuffle()">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle music">
                    </button>
                    <button class="controlButton previous" title="Previous song" onclick="prevSong()">
                        <img src="assets/images/icons/previous.png" alt="Previous song">
                    </button>
                    <button class="controlButton play" title="Play song" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play song">
                    </button>
                    <button class="controlButton pause" title="Pause song" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause song">
                    </button>
                    <button class="controlButton next" title="Next song" onclick="nextSong()">
                        <img src="assets/images/icons/next.png" alt="Next song">
                    </button>
                    <button class="controlButton repeat" title="Repeat song" onclick="setRepeat()">
                        <img src="assets/images/icons/repeat.png" alt="Repeat song">
                    </button>
                </div>
                <div class="playbackBar">
                    <span class="progressTime current">0.00</span>
                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>
                    <span class="progressTime remaining">0.00</span>
                </div>
            </div>
        </div>

        <div id="nowPlayingRight">
            <div class="volumeBar">
                <button class="controlButton volume" title="Volume button" onclick="setMute()">
                    <img src="assets/images/icons/volume.png" alt="Volume button">
                </button>
                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>