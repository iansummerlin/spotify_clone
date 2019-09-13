<?php
    /* Heroku ClearDB Database url */
    // mysql://b8f8d855c93d08:2a552266@us-cdbr-iron-east-02.cleardb.net/heroku_669bd6ce71fcdec?reconnect=true
    // mysql --host=us-cdbr-iron-east-02.cleardb.net --user=b8f8d855c93d08 --password=2a552266 --reconnect heroku_669bd6ce71fcdec < spotify.sql

    ob_start();
    session_start();
    
    $timezone = date_default_timezone_set("Europe/London");
    // $con = mysqli_connect("localhost", "root", "andrino-1995", "spotify" ); // when using local phpmyadmin database
    $con = mysqli_connect("us-cdbr-iron-east-02.cleardb.net", "b8f8d855c93d08", "2a552266", "heroku_669bd6ce71fcdec"); // when using ClearDB database with heroku


    if(mysqli_connect_errno()) {
        echo "Failed to connect" . mysqli_connect_errno();
    }