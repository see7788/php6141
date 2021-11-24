<?php

namespace app;

use Exception;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use GatewayWorker\Lib\Gateway as Cli;

class UserHttp extends BaseOnWorkerStart
{
    public  function onConnect(TcpConnection $connection)
    {
        echo $connection->worker->name . 'onConnect;';
        //$connection->send('onConnect');
    }

    public  function onMessage(TcpConnection $connection, Request $request)
    {
        try {
            $db = $request->get();
            $path = $request->ext_router();
            $db['api'] = $path;
            switch ($path) {
                case 'favicon.ico':
                    return false;
                case 'index':
                    return $connection->ext_send_socketDemo('ws://39.97.216.195:6007');
                case 'all':
                   // $sbIds=$request->get('sbIds',[]);
                    $this->redis->hGetAll('wsj.*', function ($data) use ($db, $connection) {
                        $db['success'] = $data;
                        $connection->ext_send_json_encode($db);
                    });
                    break;
                default:
                    $db['routerInfo'] = 404;
                    $connection->ext_send_json_encode($db);
            }
        } catch (Exception $e) {
            echo $connection->worker->name . ' catch;';
        }
    }

    /* public static function onMessage2($client_id, $message)
     {
         self::$http->get('http://doc3.workerman.net/1341411',
             function ($response) {
                 Cli::sendToAll(json_encode($response->getBody()));
             },
             function ($exception) {
                 echo $exception;
             });

         $all_tables = self::$pdo->query('show tables');
         json_encode($all_tables);
         self::$redis->set('key', 'value');
         self::$redis->get('key', function ($c) {
             var_dump($c);
         });
         try {
             Cli::sendToAll("$client_id said $message\r\n");
         } catch (\Exception $e) {
             echo $e;
         }
     }*/
}