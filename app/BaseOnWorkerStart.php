<?php

namespace app;
use Workerman\Worker;
use Workerman\Http\Client as AsyncHttpCli;
use Workerman\MySQL\Connection as PdoCli;
use Workerman\Redis\Client as RedisCli;
class BaseOnWorkerStart
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
        $this->pdo=new PdoCli(...ConfigStart::mysqlConfig );
        $this->redis = new RedisCli('redis://'.ConfigStart::redisConfig);
        $this->http = new AsyncHttpCli();
      //  echo $c->name.$c->id.'Start;';
    }
}