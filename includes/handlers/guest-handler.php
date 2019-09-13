<?php
    if (isset($_POST['signInGuest'])) {
        $username = 'user12734';
        $password = 'password';

        $result = $account->login($username, $password);

        if ($result) {
            $_SESSION['userLoggedIn'] = $username;
            header("Location: index.php");
        }
    }