<?php

require_once __DIR__.'/../../app/app.php';

$Auth->redirectIfNotAuthenticated();

if (!$PlanetConfig->isInstalled()) {
    die('<p>' . _g('You might want to <a href="../install.php">install moonmoon</a>.') . '</p>');
}

if (isset($_POST['purge'])) {
    Cache::setStore($PlanetConfig->getCacheDir() . '/');
    Cache::pruneAll();
}

header('Location: administration.php');
die();
