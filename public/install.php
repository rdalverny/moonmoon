<?php
require_once '../app/app.php';

// This is an helper function returning an html table row to avoid code duplication
function installStatus(string $str, string $msg, bool $result) : string
{
    $class = ($result) ? 'ok' : 'fail';
    return '<tr><td>' . $str . '</td><td class="' . $class . '">' . $msg . '</td></tr>' . "\n";
}

// If the config file exists and the auth variables are set, moonmoon is already installed
if ($PlanetConfig->isInstalled()) {
    $status = 'installed';
} elseif (isset($_POST['url'])) {
    // Do no try to use the file of an invalid locale
    if (strstr($_POST['locale'], '..') !== false
    || !file_exists(__DIR__ . "/app/l10n/${_REQUEST['locale']}.lang")) {
        $_POST['locale'] = 'en';
    }

    $save = array();
    //Save config file
    $config = array_merge(PlanetConfig::$defaultConfig, [
        'url'    => $_POST['url'],
        'name'   => filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS),
        'locale' => $_POST['locale'],
    ]);

    $PlanetConfig->setConfig($config);
    $save['config'] = (int) $PlanetConfig->saveConfig();
    OpmlManager::save(new Opml(), $PlanetConfig->getOpmlFile());
    $save['password'] = (int) $PlanetConfig->saveAdminPassword(hash('sha256', $_POST['password']));

    if (0 != ($save['config'] + $save['password'])) {
        $status = 'installed';
    }
} else {
    // We start by malking sure we have PHP7 as a base requirement
    if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
        $strInstall = installStatus('Server is running at least PHP 7.2', 'OK', true);
        $strRecommendation = '';
    } else {
        $strInstall = installStatus('Server is running at least PHP 7.2', 'FAIL', false);
        $strRecommendation = '<li>Check your server documentation to activate at least PHP 7.2</li>' . "\n";
    }

    $required_extensions = [
        'dom',       // moonmoon, simplepie
        'curl',      // simplepie
        'iconv',     // simplepie
        'libxml',    // moonmoon, simplepie
        'mbstring',  // simplepie
        'pcre',      // moonmoon
        'xml',       // moonmoon, simplepie
        'xmlreader', // moonmoon, simplepie
        'zlib'       // simplepie
    ];
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext) === true) {
            $strInstall .= installStatus("PHP extension <code>$ext</code> is present", 'OK', true);
        } else {
            $strInstall .= installStatus("PHP extension <code>$ext</code> is present", 'FAIL', false);
            $strRecommendation .= "<li>Install PHP extension <code>$ext</code> on your server</li>\n";
        }
    }

    $test_dirs = [
        PlanetConfig::config_path($sitePrefix),
        $PlanetConfig->getCacheDir()
    ];

    foreach ($test_dirs as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                $strInstall .= installStatus("Could not create directory <code>$dir</code>\n", 'FAIL', false);
                $strRecommandation .= "<li>Make sure <code>$dir</code> can be created\n";
            } else {
                $strInstall .= installStatus("Could create directory <code>$dir</code>\n", 'OK', true);
            }
        } else {
            $strInstall .= installStatus("Directory exists <code>$dir</code>\n", 'OK', true);
        }
    }

    // Writable file requirements
    $tests = array(
        $PlanetConfig->getConfigFile(),
        $PlanetConfig->getOpmlFile(),
        $PlanetConfig->getAuthInc(),
        $PlanetConfig->getCacheDir() . '/test_cache'
    );

    // We now test that all required files and directories are writable.
    foreach ($tests as $filename) {
        if (touch($filename)) {
            $strInstall .= installStatus("<code>$filename</code> is writable", 'OK', true);
            if (is_file($filename)) {
                unlink($filename);
            }
        } else {
            $strInstall .= installStatus("<code>$filename</code> is writable", 'FAIL', false);
            $strRecommendation .= "<li>Make <code>$filename</code> writable with CHMOD</li>\n";
        }
    }

    // We can now decide if we install moonmoon or not
    $status = ($strRecommendation != '') ? 'error' : 'install';
}

require_once views_path('install.tpl.php');
