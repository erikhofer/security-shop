<?php

require_once 'includes/Routing.php';
require_once 'includes/User.php';
?>
    <h1>Create an account</h1>
<?php

if (isset($_POST['submit'])) {
    $success = User::createUser($_POST);
    if($success) {
        $user = User::login($_POST['email'], $_POST['password']);
        if ($user !== null) {
            User::startUserSession($user);
            Routing::redirect('home');
        } else {
            ?>
            <div class="alert alert-danger">That did not work</div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-danger">Something went wrong :(</div>
        <?php
    }
} else {
    ?>
    <form action="<?= Routing::getUrlToSite('register'); ?>" method="post">
        <div class="form-group">
            <label for="email">E-Mail Address</label>
            <input required type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input required type="password" name="password" class="form-control">
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="firstname">First name</label>
                    <input required type="text" name="firstname" class="form-control">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="lastname">Last name</label>
                    <input required type="text" name="lastname" class="form-control">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input required type="text" name="address" class="form-control">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Sign up!</button>
    </form>
    <?php
}
?>