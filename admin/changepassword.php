<?php

require_once __DIR__.'/../app/app.php';
require_once __DIR__.'/inc/auth.inc.php';

if ($csrf->verify($_POST['_csrf'], 'frmPassword') && isset($_POST['password']) && ('' != $_POST['password'])) {
    $out = sprintf('<?php $login="admin"; $password="%s"; ?>', hash('sha256', $_POST['password']));
    file_put_contents(__DIR__.'/inc/pwd.inc.php', $out);
    die("Password changed. <a href='administration.php'>Login</a>");
} else {
    die('Can not change password');
}
