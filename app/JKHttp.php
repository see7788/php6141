<?php

namespace app;

use Exception;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use GatewayWorker\Lib\Gateway as Cli;

class JKHttp extends BaseOnWorkerStart
{
    public  function onConnect(TcpConnection $connection)
    {
        echo $connection->worker->name . '/onConnect;';
        //  $connection->send('onConnect');
    }

    public  function onMessage(TcpConnection $connection, Request $request)
    {
        try {
            $db = $request->get();
            $path = $request->ext_router();
            $db['api'] = $path;
            switch ($path) {
                case 'index':
                    break;
                case 'input':
                    $db['now'] = $db['in'] - $db['out'];
                    $this->redis->hMSet(
                        'wsj.' . $db['sbId'],
                        $db,
                    );
                    break;
                default:
                    $db['routerInfo'] = 404;
            }
            Cli::sendToAll(json_encode($db));
            $connection->ext_send_json_encode($db);
        } catch (Exception $e) {
            echo $connection->worker->name . ' catch;';
        }
    }
}