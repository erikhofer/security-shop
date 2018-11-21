<?php
    require_once 'includes/User.php';
    User::logout();
    Routing::redirect('home');
?>
