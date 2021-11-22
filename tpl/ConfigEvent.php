<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);
namespace tpl;
use Workerman\Http\Client as HttpCli;
use Workerman\Redis\Client as RedisCli;
use Workerman\MySQL\Connection as PdoCli;
use GatewayWorker\Lib\Gateway as Cli;
class ConfigEvent
{
    /**
     * @var PdoCli;
     */
    static $pdo;
    /**
     * @var HttpCli
     */
    static $http;
    /***
     * @var RedisCli
     */
    static $redis;

    public static function onWorkerStart()
    {

        self::$pdo=new PdoCli(
            '127.0.0.1',
            '3306',
            'douyintaobao',
            '8x2BXw5PrEw8PKyD',
            'douyintaobao'
        );
        self::$redis = new RedisCli('redis://127.0.0.1:6379');
        self::$http = new HttpCli();
    }
    public static function onWebSocketConnect($client_id, $data)
    {
        var_export($data);
    }
    public static function onConnect($client_id)
    {
        Cli::sendToClient($client_id,json_encode(['id'=>$client_id]));
       // Gateway::sendToAll("$client_id login\r\n");
    }

    public static function onMessage($client_id, $message)
    {
        self::$http->get('http://doc3.workerman.net/1341411',
            function ($response) {
                Cli::sendToAll(json_encode($response->getBody()));
            },
            function ($exception) {
                echo $exception;
            });

        $all_tables = self::$pdo->query('show tables');
        //json_encode($all_tables);
         /* self::$redis->set('key', 'value');
        self::$redis->get('key',function ($c){
            var_dump($c);
        });*/
        try {
            Cli::sendToAll("$client_id said $message\r\n");
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public static function onClose($client_id)
    {
        try {
            Cli::sendToAll("$client_id logout\r\n");
        } catch (\Exception $e) {
            echo $e;
        }
    }
}
