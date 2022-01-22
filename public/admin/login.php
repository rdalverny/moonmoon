<?php

require_once __DIR__ . '/../../app/app.php';

if (isset($_POST['password'])) {
    session_regenerate_id();

    $hash_pwd = hash('sha256', $_POST['password']);

    // check if old moonmoon was installed and convert stored password
    // from md5 to current hash function
    $md5_pwd  = md5($_POST['password']);
    $passfile = config_path('pwd.inc.php');
    include($passfile);

    if ($md5_pwd == $password) {
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
