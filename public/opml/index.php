<?php
require_once '../../app/app.php';

if (!file_exists($PlanetConfig->getOpmlFile())) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

header('Content-Type: text/xml; charset=utf-8');
readfile($PlanetConfig->getOpmlFile());
