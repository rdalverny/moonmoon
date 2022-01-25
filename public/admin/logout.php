<?php

require_once __DIR__ . '/../../app/app.php';

if (!$PlanetConfig->isInstalled()) {
    die('<p>' . _g('You might want to <a href="../install.php">install moonmoon</a>.') . '</p>');
}

setcookie('auth', '', time() - 3600);
session_destroy();
session_regenerate_id();

header('Location: login.php');
die();
