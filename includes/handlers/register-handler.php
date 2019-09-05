<?php 
        
    function sanitiseFormUsername($inputText) {
        $inputText = strip_tags($inputText);
        $inputText = str_replace(" ", "", $inputText);
        return $inputText;
    }

    function sanitiseFormString($inputText) {
        $inputText = strip_tags($inputText);
        $inputText = str_replace(" ", "", $inputText);
        $inputText = ucfirst(strtolower($inputText));
        return $inputText;
    }

    function sanitiseFormPassword($inputText) {
        $inputText = strip_tags($inputText);
        return $inputText;
    }

    if (isset($_POST['registerButton'])) {
        $username = sanitiseFormUsername($_POST['username']);
        $firstName = sanitiseFormString($_POST['firstName']);
        $lastName = sanitiseFormString($_POST['lastName']);
        $email = sanitiseFormString($_POST['email']);
        $email2 = sanitiseFormString($_POST['email2']);
        $password = sanitiseFormPassword($_POST['password']);
        $password2 = sanitiseFormPassword($_POST['password2']);

        $wasSuccessful = $account->register($username, $firstName, $lastName, $email, $email2, $password, $password2);

        if($wasSuccessful) {
            $_SESSION['userLoggedIn'] = $username;
            header("Location: index.php");
        }
        
    }
?>
