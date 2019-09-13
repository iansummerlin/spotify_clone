<?php 

    include("../../config.php");

    if(!isset($_POST['username'])) {
        echo "Error could not set username";
        exit();
    }

    if (!isset($_POST['oldPassword']) || !isset($_POST['newPassword1']) || !isset($_POST['newPassword2'])) {
        echo "Please set all passwords!";
        exit();
    }

    if (isset($_POST['oldPassword']) == "" || isset($_POST['newPassword1']) == "" || isset($_POST['newPassword2']) == "") {
        echo "Please fill in all fields!";
        exit();
    }

    $username = $_POST['username'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword1 = $_POST['newPassword1'];
    $newPassword2 = $_POST['newPassword2'];

    $oldMd5 = md5($oldPassword);

    $passwordCheckQuery = mysqli_query($con, "SELECT password FROM users WHERE username = '$username' AND password = '$oldMd5'");

    if (mysqli_num_rows($passwordCheckQuery) != 1) {
        echo "Incorrect password";
        exit();
    }

    if ($newPassword1 != $newPassword2) {
        echo "Your new passwords do not match!";
        exit();
    }

    if (preg_match('/[^A-Za-z0-9]/', $newPassword1)) {
        echo "Your password must only contain numbers and letters";
        exit();
    }

    if (strlen($newPassword1) > 30 || strlen($newPassword1) < 5) {
        echo "Your password must be between 5 and 30 characters";
        exit();
    }

    $newMd5 = md5($newPassword1);

    $udatePasswordQuery = mysqli_query($con, "UPDATE users SET password = '$newMd5' WHERE username = '$username'");

    echo "Update successful";





?>