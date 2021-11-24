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
namespace app;
use GatewayWorker\Lib\Gateway as Cli;
class BusinessWebSocket
{
    public static function onWebSocketConnect($client_id, $data)
    {
       echo 'onWebSocketConnect';
        Cli::sendToClient($client_id,json_encode([
            'api'=>'userInit',
            'id'=>$client_id
        ]));
       // var_export($data);
       // var_export($_SERVER);
       // var_export($_SESSION);
    }

    public static function onConnect($client_id)
    {
        var_export('onConnect');
        Cli::sendToClient($client_id,json_encode([
            'api'=>'userInit',
            'id'=>$client_id
        ]));
    }

    public static function onMessage($client_id, $message)
    {
       var_export($message);
    }
}
