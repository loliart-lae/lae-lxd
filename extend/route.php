<?php

use Workerman\Protocols\Http\Response;

class Exec
{
    static public function route($request)
    {
        switch ($request->path()) {
            case '/lxd/init':
                return Lxd::init($request);
                break;

            case '/lxd/start':
                return Lxd::start($request->get('id'));
                break;

            case '/lxd/stop':
                return Lxd::stop($request->get('id'));
                break;

            case '/lxd/delete':
                return Lxd::delete($request->get('id'));
                break;

            case '/lxd/forward':
                return Lxd::forward($request);
                break;

            case '/lxd/forward_delete':
                return Lxd::forward_delete($request);
                break;

            case '/lxd/resize':
                return Lxd::resize($request);
                break;

            default:
                return 404;
                break;
        }
    }
}
