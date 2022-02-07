<?php

error_reporting(0);

require_once __DIR__.'/../vendor/autoload.php';

$sitePrefix = empty(getenv('MULTISITE')) ? '' : getMultiSitePrefix($_SERVER['REQUEST_URI']);

$moon_version = trim(file_get_contents(__DIR__.'/../VERSION'));

session_start();

$PlanetConfig = PlanetConfig::load($sitePrefix);
$Auth = new Authentication($PlanetConfig->getAuthFile());
$Planet = new Planet($PlanetConfig);

if ($PlanetConfig->getDebug()) {
    error_reporting(E_ALL);
}

$l10n = new Simplel10n($PlanetConfig->getLocale());
$csrf = new CSRF();
