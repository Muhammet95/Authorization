<?php

use App\AuthController as AuthControllerAlias;

require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['PATH_INFO'] === '/auth/me') {
    $auth = new AuthControllerAlias();
    echo json_encode(['auth' => $auth->me()]);
    return 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/login') {
    $auth = new AuthControllerAlias();
    echo json_encode($auth->login());
    return 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/logout') {
    $auth = new AuthControllerAlias();
    echo json_encode(['logout' => $auth->logout()]);
    return 0;
}

not_found();

function not_found()
{
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cgi')
        header("Status: 404 Not Found");
    else
        header("HTTP/1.1 404 Not Found");
}