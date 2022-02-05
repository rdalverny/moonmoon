<?php

require_once '../app/app.php';

if (!$PlanetConfig->isInstalled()) {
    die();
}

$opml = OpmlManager::load($PlanetConfig->getOpmlFile());
foreach ($opml->entries as $source) {
    if ($source['feed'] == $_GET['url']) {
        $feed = new PlanetFeed(
            '',
            $source['feed'],
            '',
            false,
            $PlanetConfig->getCacheDir()
        );
        $Planet->addPerson($feed);
        $Planet->download(1.0);
        header('Content-type: image/png');
        readfile(__DIR__ . '/custom/img/feed.png');
        die();
    }
}
