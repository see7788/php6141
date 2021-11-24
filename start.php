<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\Index;
use Workerman\Worker;
ini_set('display_errors', 'on');

if(strpos(strtolower(PHP_OS), 'win') === 0)
{
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    new Index();
    define('GLOBAL_START', 1);
    Worker::runAll();
}
/*

php start.php start //以debug（调试）方式启动
php start.php start -d //以daemon（守护进程）方式启动
php start.php stop //停止
php start.php restart //重启
php start.php reload //平滑重启
php start.php status //查看状态
php start.php connections //查看连接状态（需要Workerman版本>=3.5.0）
lsof -i:端口号  //查看端口占用
kill -9 PID   //解除端口占用

sudo kill -9 $(lsof -i:端口号 -t)

netstat -apn | grep 6006
kill pid
*/