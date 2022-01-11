<?php

/**
 * Register polyfills for old PHP versions.
 *
 * This way, the real function will only be called if it
 * is available, and we won't force the use of our own
 * implementation.
 */
function register_polyfills()
{
}

register_polyfills();

/**
 * Path to the _custom_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 */
function custom_path($file = '')
{
    return __DIR__.'/../custom' . (!empty($file) ? '/'.$file : '');
}

/**
 * Path to the _views_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 */
function views_path($file = '')
{
    return custom_path('views/' . $file);
}

/**
 * Path to the _admin_ directory.
 *
 * @param  string $file Append this filename to the returned path.
 * @return string
 */
function admin_path($file = '')
{
    return __DIR__.'/../admin' . (!empty($file) ? '/'.$file : '');
}

/**
 * Is moonmoon installed?
 *
 * @return bool
 */
function is_installed()
{
    return file_exists(custom_path('config.yml')) && file_exists(custom_path('people.opml'));
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
function removeCustomFiles()
{
    $toRemove = [
        custom_path('config.yml'),
        custom_path('people.opml'),
        custom_path('people.opml.bak'),
        custom_path('cache')
    ];

    foreach ($toRemove as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
