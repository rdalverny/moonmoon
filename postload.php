<?php

require_once __DIR__.'/app/app.php';

if (!$PlanetConfig::isInstalled()) {
    die();
}

$xml = new SimpleXMLElement(file_get_contents($PlanetConfig->getOpmlFile()));

foreach ($xml->xpath('/opml/body/outline[@xmlUrl]') as $element) {
    if ($element->attributes()->xmlUrl == $_GET['url']) {
        $person = new PlanetFeed(
            '',
            $_GET['url'],
            '',
            false
        );
        $Planet->addPerson($person);

        $Planet->download(1);
        header('Content-type: image/png');
        readfile(custom_path('img/feed.png'));
        die();
    }
}

echo 'Updating this URL is not allowed.';
