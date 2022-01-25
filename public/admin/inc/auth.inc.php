<?php

include $PlanetConfig->getAuthInc();

if (!Planet::authenticateUser($_COOKIE['auth'] ?? '', $password)) {
    setcookie('auth', '', time() - 3600);
    header('Location: login.php');
    die();
}
