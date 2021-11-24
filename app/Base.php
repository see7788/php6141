<?php

namespace app;
use Workerman\Worker;
use Workerman\Http\Client as AsyncHttpCli;
use Workerman\MySQL\Connection as PdoCli;
use Workerman\Redis\Client as RedisCli;
class Base
{
    /**
     * @var PdoCli;
     */
    public PdoCli $pdo;
    /***
     * @var RedisCli
     */
    public RedisCli $redis;
    /**
     * @var AsyncHttpCli
     */
    public AsyncHttpCli $http;

    public function onWorkerStart(Worker $c)
    {
        $this->pdo=new PdoCli(
            '127.0.0.1',
            '3306',
            'douyintaobao',
            '8x2BXw5PrEw8PKyD',
            'douyintaobao'
        );
        $this->redis = new RedisCli('redis://127.0.0.1:6379');
        $this->http = new AsyncHttpCli();
      //  echo $c->name.$c->id.'Start;';
    }
}