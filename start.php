<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\ConfigData;
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
    new ConfigData();
    define('GLOBAL_START', 1);
    Worker::runAll();
}
