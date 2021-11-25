<?php

namespace app;
use Workerman\Http\Client as AsyncHttpCli;
use Workerman\MySQL\Connection as PdoCli;
use Workerman\Redis\Client as RedisCli;
class BaseOnWorkerStart
{
    public const uidCookeFileName='pageNow';
    /**
     * @var PdoCli;
     */
    public static PdoCli $pdo;
    /***
     * @var RedisCli
     */
    public static RedisCli $redis;
    /**
     * @var AsyncHttpCli
     */
    public static AsyncHttpCli $http;

    public static function onWorkerStart($c)
    {
      //  var_export($c);
        self::$pdo=new PdoCli(...ConfigStart::mysqlConfig );
        self::$redis = new RedisCli('redis://'.ConfigStart::redisConfig);
        self::$http = new AsyncHttpCli();
      //  echo $c->name.$c->id.'Start;';
    }
}