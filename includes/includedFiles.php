<?php 
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        include("includes/config.php");
        include("includes/classes/User.php");
        include("includes/classes/Artist.php");
        include("includes/classes/Album.php");
        include("includes/classes/Song.php");
        include("includes/classes/Playlists.php");

        (isset($_GET['userLoggedIn'])) ? $userLoggedIn = new User($con, $_GET['userLoggedIn']) : alert("Username variable not found.");
    } else {
        include("includes/header.php");
        include("includes/footer.php");
        $url = $_SERVER['REQUEST_URI'];
        echo "<script>openPage('$url');</script>";
        exit();
    }
?>