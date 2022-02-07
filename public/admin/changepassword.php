<?php

require_once __DIR__.'/../app/app.php';

$Auth->redirectIfNotAuthenticated();

if ($csrf->verify($_POST['_csrf'], 'frmPassword') && isset($_POST['password']) && ('' != $_POST['password'])) {
    $Auth->changePassword($_POST['password']);
    redirect('administration.php');
}

die('Cannot change password');
