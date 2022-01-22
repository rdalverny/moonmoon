<?php
require_once '../app/app.php';

//Load OPML
if (0 < $Planet->loadOpml($PlanetConfig->getOpmlFile())) {
    $Planet->download(1.0);
}

if ($PlanetConfig->getDebug()) {
    foreach ($Planet->errors as $error) {
        echo $error->toString() . "\n";
    }
}
