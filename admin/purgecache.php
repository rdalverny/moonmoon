<?php

require_once __DIR__.'/../app/app.php';
require_once __DIR__.'/inc/auth.inc.php';

if (isset($_POST['purge'])) {
    $dir = __DIR__.'/../cache/';

    $dh = opendir($dir);

    while ($filename = readdir($dh)) {
        if ($filename == '.' or $filename == '..') {
            continue;
        }

        $file = $dir . DIRECTORY_SEPARATOR . $filename;
        if (is_file($file) && filemtime($file) < time()) {
            unlink($file);
        }
    }
}

header('Location: administration.php');
die();
