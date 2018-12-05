<?php

require_once 'includes/Routing.php';
require_once 'includes/User.php';
require_once 'includes/CSRF.php';

?>
<h1>Login</h1>
<?php

if (isset($_POST['submit'])) {
    CSRF::expectValidTokenInRequest();
    $user = User::login($_POST['email'], $_POST['password']);
    if ($user !== null) {
        User::startUserSession($user);
        Routing::redirect('home');
    } else {
        ?>
        <div class="alert alert-danger">That did not work</div>
        <?php
    }
}

if(!User::isLoggedIn()) {
    ?>
    <form action="<?= Routing::getUrlToSite('login'); ?>" method="post">
        <div class="form-group">
            <label for="email">E-Mail Address</label>
            <input required type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input required type="password" name="password" class="form-control">
        </div>
        <?= CSRF::getFormField(); ?>
        <button type="submit" name="submit" class="btn btn-primary">Login</button>
    </form>
    <?php
} else {
    ?>
<div class="alert alert-warning">You are already logged in!</div>
<?php
}