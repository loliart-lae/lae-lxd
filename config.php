<?php

require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/extend/route.php';
require_once __DIR__ . '/extend/lxd.php';

$token = 'AIWRQBA29q8G21aviu1';
define('__TOKEN__', $token);



function console($msg) {
    $date = date('Y-m-d H:i:s');
    echo PHP_EOL . "[{$date}] " . $msg . PHP_EOL;
}