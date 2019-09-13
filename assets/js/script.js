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

$(document).click(function(click) {
    var target = $(click.target);
    if (!target.hasClass("item") && !target.hasClass("optionsButton")) hideOptionsMenu(); 
});

$(window).scroll(function() {
    hideOptionsMenu();
});

$(document).on("change", "select.playlist", function() {
    var select = $(this);
    var playlistId = select.val();
    var songId = select.prev(".songId").val();

    $.post("includes/handlers/ajax/addToPlaylist.php", { playlistId: playlistId, songId: songId })
    .done(function(error) {
        if (error != "") {
            alert(error);
            return;
        }

        hideOptionsMenu();
        select.val("");
    });
});

function logout() {
    $.post("includes/handlers/ajax/logout.php", function() {
        location.reload();
    });
}

function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordclass2) {
    var oldPassword = $("." + oldPasswordClass).val();
    var newPassword1 = $("." + newPasswordClass1).val();
    var newPassword2 = $("." + newPasswordclass2).val();

    $.post("includes/handlers/ajax/updatePassword.php", 
        { oldPassword: oldPassword, 
            newPassword1 : newPassword1,
            newPassword2 : newPassword2,
            username: userLoggedIn, })
    .done(function(response) {
        $("." + oldPasswordClass).nextAll("span.message").text(response);
    });
} 

function updateEmail(emailClass) {
    var emailValue = $("." + emailClass).val();

    $.post("includes/handlers/ajax/updateEmail.php", { email: emailValue, username: userLoggedIn })
    .done(function(response) {
        $("." + emailClass).nextAll("span.message").text(response);
    });
} 

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

function removeFromPlaylist(button, playlistId) {
    var songId = $(button).prev(".songId").val();
    
    if (songId) {
        $.post("includes/handlers/ajax/removeFromPlaylist.php", { playlistId: playlistId, songId: songId })
        .done(function(error) {
            if (error != "") {
                alert(error);
                return;
            }
            openPage("playlist.php?id=" + playlistId);
        });
    }
}

function deletePlaylist(playlistId) {
    var deletePopup = confirm("Are you sure you want to delete this playlist?");

    if (deletePopup) {
        $.post("includes/handlers/ajax/deletePlaylist.php", { playlistId: playlistId })
        .done(function(error) {
            if (error != "") {
                alert(error);
                return;
            }
            openPage("myMusic.php");
        });
    }
}

function showOptionsMenu(button) {
    var songId = $(button).prev(".songId").val();
    var menu = $(".optionsMenu");
    var menuWidth = menu.width();
    menu.find(".songId").val(songId);

    var scrollTop = $(window).scrollTop(); // Distance from top of window to document
    var elementOffset = $(button).offset().top; // Distance from top of document

    var top = elementOffset - scrollTop;
    var left = $(button).position().left; 

    menu.css({ "top": top + "px", "left": left - menuWidth + "px", "display": "inline" });
}

function hideOptionsMenu() {
    var menu = $(".optionsMenu");
    if (menu.css("display") != "none") menu.css({ "display": "none"});
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
