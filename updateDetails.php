<?php include("includes/includedFiles.php"); ?>

<div class="userDetails">
    <div class="container borderBottom">
        <h2>Email</h2>
        <input type="text" name="email" placeholder="Email address..." class="email" value="<?php echo $userLoggedIn->getEmail(); ?>">
        <span class="message"></span>
        <button class="button" onclick="updateEmail('email')">Save</button>
    </div>
</div>

<div class="userDetails">
    <div class="container">
        <h2>Password</h2>
        <input type="password" name="oldPassword" placeholder="Current password" class="oldPassword">
        <input type="password" name="newPassword1" placeholder="New password" class="newPassword1">
        <input type="password" name="newPassword2" placeholder="Confirm password" class="newPassword2">

        <span class="message"></span>
        <button class="button" onclick="updatePassword('oldPassword', 'newPassword1', 'newPassword2')">Save</button>
    </div>
</div>