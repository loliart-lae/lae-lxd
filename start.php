<?php

use Workerman\Worker;

require_once __DIR__ . '/config.php';

$worker = new Worker("http://0.0.0.0:821");
$worker->count = 1;

$worker->onWorkerStart = function ($worker) {
    $worker->onMessage = function ($con, $request) {
        if ($request->get('token') == __TOKEN__) {
            $con->send(json_encode(Exec::route($request)));
        } else {
            $con->send(null);
        }
    };
};

Worker::runAll();