<?php

namespace app;

use GatewayWorker\Register;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Lib\Gateway as PhpCli;
use Workerman\Worker;

//要重命名replace_vendor用到的vendor里的类成其他类名，如类名Old
//不然无法使用拓展功能哦
class ConfigStart
{
    public const mysqlConfig = [
        '127.0.0.1',
        '3306',
        'douyintaobao',
        '8x2BXw5PrEw8PKyD',
        'douyintaobao'
    ];
    public const redisConfig = '127.0.0.1:6379';
    public const registerIp = '127.0.0.1';
    public const registerPort = '6006';
    public const registerKey = '6006RegisterPass';

    function __construct()
    {
        $registerIp = self::registerIp;
        $registerAddress="$registerIp:".self::registerPort;
        //注册Serve,只能一个
        $r = new Register("text://".$registerAddress);
        $r->name = self::registerPort . 'Register';
        $r->secretKey = self::registerKey;//秘钥

        //调度计算Serve：可以分布式，添加多个服务器运行BusinessWorker分摊计算量
        $b = new BusinessWorker();
        $b->registerAddress = $registerAddress;
        $b->secretKey = self::registerKey;//秘钥
        $b->eventHandler = 'app\BusinessEvent';
        $b->name = self::registerPort . 'BusinessWorker';
        $b->count = 4;

        //网络IoServe：可以分布式，添加多台服务器
        $g = new Gateway("websocket://0.0.0.0:6007");//页面端访问
        $g->registerAddress =$registerAddress;
        $g->name = self::registerPort . 'Gateway';
        $g->secretKey = self::registerKey;//秘钥
        $g->startPort = 2900;//内部通讯起始端口，每个 gateway 实例应该都不同，步长1000
        $g->count = 4;
        $g->pingInterval = 10;// 心跳间隔
        $g->pingData = '{"api":"心跳"}';// 心跳数据
        $g->lanIp = $registerIp;//本机ip，如果是分布式部署，需要设置成本机 IP

        //phpCli，允许多ip， 使用GatewayWorker\Lib\Gateway控制GatewayWorker\Gateway
        PhpCli::$registerAddress =$registerAddress;
        PhpCli::$secretKey = self::registerKey;//秘钥

        $u = new Worker('http://0.0.0.0:6008');
        $u->count = 4;
        $u->name = self::registerPort . 'UserHttp';
        self::bindOns($u, new UserHttp());

        //恒华4d
        $j = new Worker('http://0.0.0.0:6009');
        $j->count = 4;
        $j->name = self::registerPort . 'JKHttp';
        self::bindOns($j, new JKHttp());
    }

    private static function bindOns(Worker $workerObj, $classObj): void
    {
        $callback_map = [
            'onWorkerStart',
            'onConnect',
            'onMessage',
            'onClose',
            'onError',
            'onBufferFull',
            'onBufferDrain',
            'onWorkerStop',
            'onWebSocketConnect'
        ];
        foreach ($callback_map as $name) {
            if (method_exists($classObj, $name)) {
                $workerObj->$name = [$classObj, $name];
            }
        }
    }

}