<?php

/**
 * Register polyfills for old PHP versions.
 *
 * This way, the real function will only be called if it
 * is available, and we won't force the use of our own
 * implementation.
 */
function register_polyfills() : void
{
}

register_polyfills();

/**
 * Path to the _custom_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 * @deprecated
 */
function custom_path($file = '') : string
{
    return __DIR__.'/../custom' . (!empty($file) ? '/'.$file : '');
}

/**
 * Path to the _views_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 * @deprecated
 */
function views_path($file = '') : string
{
    return custom_path('views/' . $file);
}

/**
 * Path to the _admin_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 * @deprecated
 */
function admin_path($file = '') : string
{
    return __DIR__.'/../admin' . (!empty($file) ? '/'.$file : '');
}

/**
 * Path to the _config_ directory.
 *
 * @param  string $file Append this filename to the returned path
 * @param  string $site Append this site as a sub-directory before the file
 * @return string
 * @deprecated Use PlanetConfig::config_path() instead
 */
function config_path(string $file = '', string $site = '') : string
{
    $path = __DIR__ . '/../custom/config';
    if (!empty($site)) {
        $path .= '/' . $site;
    }
    if (!empty($file)) {
        $path .= '/' . $file;
    }
    return $path;
}

/**
 * @deprecated Use $PlanetConfig->getCacheDir() instead
 */
function cache_path(string $file, string $site = '') : string
{
    $path = __DIR__ . '/../cache';
    if (!empty($site)) {
        $path .= '/' . $site;
    }
    if (!empty($file)) {
        $path .= '/' . $file;
    }
    return $path;
}

/**
 * Shortcut to Simplel10n::getString().
 *
 * @param  string $str
 * @param  string $comment
 * @return string
 */
function _g($str, $comment = '')
{
    return Simplel10n::getString($str, $comment);
}

/**
 * Reset the moonmoon instance.
 */
function removeCustomFiles() : void
{
    $toRemove = [
        PlanetConfig::config_path('config.yml'),
        PlanetConfig::config_path('people.opml'),
        PlanetConfig::config_path('people.opml.bak'),
        PlanetConfig::config_path('pwd.inc.php'),
        cache_path('cache'),

        // legacy location
        custom_path('config.yml'),
        custom_path('config.yml.bak'),
        custom_path('people.opml'),
        custom_path('people.opml.bak'),
    ];

    foreach ($toRemove as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
}

 /**
  * Redirect the user to a page.
  */
function redirect(string $location = '/') : void
{
    header(sprintf('Location: %s', $location));
    die();
}

/**
 * If request URI has more than one path component, return it,
 * as it "may" be a site prefix (in case of a multisite setup).
 */
function getMultiSitePrefix(string $uri) : string
{
    error_log($uri);
    $items = explode('/', $uri);
    if (count($items) > 2) {
        return $items[1];
    }

    return '';
}

// From https://gist.github.com/gladx/62fa307eb65586b6dbaaad75273c653d
function humanFilesize(int $bytes, int $decimals = 2) : string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    $factor = floor(log($bytes, 1024));
    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . ['B', 'KB', 'MB', 'GB', 'TB', 'PB'][$factor];
}
