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
        currentPlaylist = <?php echo $jsonArray; ?>;
        audioElement = new Audio();
        setTrack(currentPlaylist[0], currentPlaylist, false);
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

    function nextSong() {
        (currentIndex == currentPlaylist.length - 1) ? currentIndex = 0 : currentIndex++;

        var trackToPlay = currentPlaylist[currentIndex];
        setTrack(trackToPlay, currentPlaylist, true);
    }

    function setTrack(trackId, newPlaylist, play) {

       $.post("includes/handlers/ajax/getSong.json.php", { songId: trackId }, (data) => {
            currentIndex = currentPlaylist.indexOf(trackId);

            var track = JSON.parse(data);

            $(".trackName span").text(track.title);

            $.post("includes/handlers/ajax/getArtist.json.php", { artistId: track.artist }, (data) => {
                var artist = JSON.parse(data);
                $(".artistName span").text(artist.name);
            });

            $.post("includes/handlers/ajax/getArtwork.json.php", { albumId: track.album }, (data) => {
                var artwork = JSON.parse(data);
                $(".albumLink img").attr("src", artwork.artworkPath);
            });

           audioElement.setTrack(track);
       });

        if (play) {
            playSong();
        }
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
                    <img src="" alt="album artwork" class="albumArtwork">
                </span>
                <span class="trackInfo">
                    <span class="trackName">
                        <span></span>
                    </span>
                    <span class="artistName">
                        <span></span>
                    </span>
                </span>
            </div>
        </div>

        <div id="nowPlayingCentre">
            <div class="content playerControls">
                <div class="buttons">
                    <button class="controlButton shuffle" title="Shuffle music">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle music">
                    </button>
                    <button class="controlButton previous" title="Previous song">
                        <img src="assets/images/icons/previous.png" alt="Previous song">
                    </button>
                    <button class="controlButton play" title="Play song" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play song">
                    </button>
                    <button class="controlButton pause" title="Pause song" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause song">
                    </button>
                    <button class="controlButton next" title="Next song">
                        <img src="assets/images/icons/next.png" alt="Next song">
                    </button>
                    <button class="controlButton repeat" title="Repeat song">
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
                <button class="controlButton volume" title="Volume button">
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