<?php 
    include('includes/includedFiles.php'); 
    (isset($_GET['term'])) ? $term = urldecode($_GET['term']) : $term = '';
?>

<div class="searchContainer">
    <h4>Search for an artist, album or song</h4>
    <input type="text" class="searchInput" value="<?php echo $term; ?>" placeholder="Start typing..." 
        onfocus="var val=this.value; this.value=''; this.value= val;">
</div>

<script>
    $(".searchInput").focus();

    $(function() {
        $(".searchInput").keyup(function() {
            clearTimeout(timer);

            timer = setTimeout(function() {
                var searchTerm = $(".searchInput").val();
                openPage("search.php?term=" + searchTerm);
            }, 2000);
        });
    });
</script>

<?php if ($term == '') exit(); ?>

<div class="tracklistContainer borderBottom">
    <h2>Songs</h2>
    <ul class="tracklist">
        <?php
            $songQuery = mysqli_query($con, "SELECT id FROM songs WHERE title LIKE '$term%' LIMIT 10");

            if (mysqli_num_rows($songQuery) == 0) {
                echo "<span class='noResults'>There are no songs found matching '" . $term . "'</span>";
            }

            $songIdArray = [];
            $i = 1;

            while($row = mysqli_fetch_array($songQuery)) {
                if ($i > 15) break;

                array_push($songIdArray, $row['id']);

                $albumSong = new Song($con, $row['id']);
                $albumArtist = $albumSong->getArtist();

                echo "
                    <li class='tracklistRow'>
                        <div class='trackCount'>
                            <img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", pagePlaylist, true)'>
                            <span class='trackNumber'>$i</span>
                        </div>

                        <div class='trackInfo'>
                            <span class='trackName'>" . $albumSong->getTitle() . "</span>
                            <span class='artistName'>" . $albumArtist->getName() . "</span>
                        </div>

                        <div class='trackOptions'>
                            <input type='hidden' class='songId' value='" . $albumSong->getId() . "'>
                            <img class='optionsButton' src='assets/images/icons/more.png' onclick='showOptionsMenu(this)'>
                        </div>

                        <div class='trackDuration'>
                            <span class='duration'>" . $albumSong->getDuration() . "</span>
                        </div>
                    </li>
                ";
                $i++;
            }
        ?>

        <script>
            var pageSongIds = '<?php echo json_encode($songIdArray); ?>';
            pagePlaylist = JSON.parse(pageSongIds);
        </script>
    </ul>
</div>

<div class="artistContainer borderBottom">
    <h2>Artists</h2>
    <?php 
        $artistQuery = mysqli_query($con, "SELECT id FROM artists WHERE name LIKE '$term%' LIMIT 10");

        if (mysqli_num_rows($artistQuery) == 0) {
            echo "<span class='noResults'>There are no artists found matching '" . $term . "'</span>";
        }

        while ($row = mysqli_fetch_array($artistQuery)) {
            $artist = new Artist($con, $row['id']);

            echo "
                <div class='searchResultRow'>
                    <div class='artistName'>
                        <span role='link' tabindex='0' onclick='openPage(\"artist.php?id=" . $artist->getId() . "\")'>
                            "
                                . $artist->getName() .
                            "
                        </span>
                    </div>
                </div>
            ";
        }
    ?>
</div>

<div class="gridViewContainer">
    <h2>Albums</h2>
    <?php 
        $albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE title LIKE '$term%' LIMIT 10");

        if (mysqli_num_rows($albumQuery) == 0) {
            echo "<span class='noResults'>There are no albums found matching '" . $term . "'</span>";
        }

        while ($row = mysqli_fetch_array($albumQuery)) {
            echo "

                <div class='gridViewItem'>
                    <span role='link' tabindex=0 onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
                        <img src='" . $row['artworkPath'] . "'>
                        <div class='gridViewInfo'>"
                            . $row['title'] ,
                        "</div>
                    </span>
                </div> 
            ";
        }
    ?>
</div>

<nav class="optionsMenu">
    <input type="hidden" class="songId">
    <?php 
        echo Playlist::getPlaylistDropdown($con, $userLoggedIn->getUsername());
    ?>
</nav>