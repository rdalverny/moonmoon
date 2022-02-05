<?php

require_once __DIR__ . '/../../app/app.php';

if (!$PlanetConfig->isInstalled()) {
    die('<p>' . _g('You might want to <a href="../install.php">install moonmoon</a>.') . '</p>');
}

if (isset($_POST['password'])) {
    session_regenerate_id();

    $hash_pwd = hash('sha256', $_POST['password']);

    // an old moonmoon may have been installed,
    // in which it would still use md5 to hash password
    $passfile = $PlanetConfig->getAuthInc();
    include($passfile);
    if (md5($_POST['password'] == $password)) {
        error_log("Migrating password from md5 to sha256");
        file_put_contents($passfile, sprintf('<?php $login="admin"; $password="%s"; ?>', $hash_pwd));
    }

    setcookie('auth', $hash_pwd);
    header('Location: index.php');
}

$page_content = <<<FRAGMENT
            <form action="" method="post" class="login">
                <fieldset>
                    <p class="field">
                        <label for="password">{$l10n->getString('Password:')}</label>
                        <input type="password" name="password" id="password"/>
                        <input type="submit" class="submit" value="{$l10n->getString('OK')}"/>
                    </p>
                </fieldset>
            </form>
FRAGMENT;

$footer_extra = <<<FRAGMENT
    <script type="text/javascript">
    <!--
    window.onload = function() {
        document.getElementById('password').focus();
    }
    -->
    </script>

FRAGMENT;

$page_id      = 'admin-login';
$admin_access = 0;

require_once __DIR__ . '/template.php';
