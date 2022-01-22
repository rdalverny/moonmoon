<?php
require_once '../../app/app.php';

if (!file_exists(config_path('people.opml'))) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

header('Content-Type: text/xml; charset=utf-8');
readfile(config_path('people.opml'));
