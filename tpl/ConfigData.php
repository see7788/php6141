<?php

namespace tpl;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Register;
use GatewayWorker\Gateway;
class ConfigData
{
    function __construct()
    {
        $registerIp = '127.0.0.1';
        $registerPort = '6006';

        //注册中心
        $r = new Register("text://0.0.0.0:$registerPort");
        $r->name = '6006Register';

        //负责计算：可以分布式，添加多个服务器运行BusinessWorker分摊计算量
        $b = new BusinessWorker();
        $b->registerAddress = "$registerIp:$registerPort";
        $b->eventHandler ='tpl\ConfigEvent';
        $b->name = '6006BusinessWorker';
        $b->count = 4;

        //负责网络Io：可以分布式，添加多台服务器
        $g = new Gateway("websocket://0.0.0.0:6007");//页面端访问
        $g->registerAddress = "$registerIp:$registerPort";
        $g->name = '6006Gateway';
        $g->startPort = 2900;//内部通讯起始端口，每个 gateway 实例应该都不同，步长1000
        $g->count = 4;
        $g->pingInterval = 10;// 心跳间隔
        $g->pingData = '{"type":"ping"}';// 心跳数据
        $g->lanIp = $registerIp;//本机ip，如果是分布式部署，需要设置成本机 IP

    }
}