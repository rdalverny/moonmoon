<?php
require_once '../app/app.php';
require_once '../app/classes/Cache.php';

if (!$PlanetConfig::isInstalled()) {
    die('<p>' . _g('You might want to <a href="install.php">install moonmoon</a>.') . '</p>');
}

$pageRole = $_GET['type'] ?? 'index';
$pageTheme = 'default';
if (!in_array($pageRole, ['index', 'archive', 'atom10'])) {
    $pageRole = 'index';
}

if ($pageRole == 'atom10') {
    /* XXX: Redirect old ATOM feeds to new url to make sure our users don't
     * loose subscribers upon upgrading their moonmoon installation.
     * Remove this check in a more distant future.
     */
    header('Status: 301 Moved Permanently', false, 301);
    header('Location: feed/');
    exit;
}

$cache_duration = $PlanetConfig->getOutputTimeout();
Cache::$enabled = ($cache_duration > 0);
Cache::setStore($PlanetConfig->getCacheDir() . '/');

if (!OutputCache::Start('html', $pageRole, $cache_duration)) {
    $items = $Planet->getFeedsItems();
    $last_modified  = (count($items)) ? $items[0]->get_date() : '';
    include_once '../custom/views/'.$pageTheme.'/'.$pageRole.'.tpl.php';
    OutputCache::End();
}

if ($PlanetConfig->getDebug()) {
    echo "<!-- \$Planet->errors:\n";
    var_dump($Planet->errors);
    echo "-->";
}
