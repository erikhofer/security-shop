<?php
require_once 'includes/User.php';
require_once 'includes/CSRF.php';

if (isset($_POST['submit'])) {
    CSRF::expectValidTokenInRequest();
    User::logout();
    FlashMessage::addMessage('You have been logged out!', FlashMessage::SEVERITY_SUCCESS);
    Routing::redirect('home');
}
?>
<h1>Do you really want to leave the security shop?</h1>
<form action="<?= Routing::getUrlToSite('logout'); ?>" method="post">
    <?= CSRF::getFormField(); ?>
    <button type="submit" name="submit" class="btn btn-warning">Logout</button>
</form>