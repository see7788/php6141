<?php

namespace app;

use Exception;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use GatewayWorker\Lib\Gateway as Cli;

class JKHttp extends BaseOnWorkerStart
{
    public function onConnect(TcpConnection $connection)
    {
        echo $connection->worker->name . '/onConnect;';
        //  $connection->send('onConnect');
    }

    public function onMessage(TcpConnection $connection, Request $request)
    {
        try {
            $path = $request->ext_router();
            $get = $request->get();
            $post = $request->post();
            $db = $get + $post;
            var_export($db);
            $db['api'] = $path;
            switch ($path) {
                case 'favicon.ico':
                case 'index':
                    Cli::sendToAll(json_encode($db));
                    break;
                case 'input':
                    //http://39.97.216.195:6009/input?fjId=0001&kw=16
                    $fjId = $request->get('fjId', '?');
                    $data = (array)json_decode($db['data']);
                    $inNum=$data['InNum'];
                    $outNum=$data['OutNum'];
                    $db2 = array(
                        'fjId' => (int)$fjId,//房间int
                        'DataDateTime'=>$data['DataDateTime'],//时间
                        'inNum' => (int)$inNum,//进
                        'outNum' =>(int)$outNum,//出
                        'nowNum' =>(int)$inNum - $outNum,//当前
                        'kwNum' =>(int) $request->get('kwNum', '?')//坑位
                    );
                    $this->redis->hMSet(
                        'fjId.' . $fjId,
                        $db2,
                    );
                    Cli::sendToAll(json_encode($db2));
                    break;
                default:
                    $db['routerInfo'] = 4004;
            }
            $connection->ext_send_json_encode($db);
        } catch (Exception $e) {
            echo $connection->worker->name . ' catch;';
        }
    }
}