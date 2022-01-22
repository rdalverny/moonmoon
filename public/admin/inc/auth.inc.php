<?php

include config_path('pwd.inc.php');

if (!Planet::authenticateUser($_COOKIE['auth'] ?? '', $password)) {
    setcookie('auth', '', time() - 3600);
    header('Location: login.php');
    die();
}
