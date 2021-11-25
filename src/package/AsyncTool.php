<?php

namespace php6141;

use Exception;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

class AsyncTool
{
    function httpProxy()
    {
        $worker = new Worker('tcp://0.0.0.0:8080');// 6 processes
        $worker->count = 6;// Worker name.
        $worker->name = 'php-http-proxy';// Emitted when data received from client.
        $worker->onMessage = function ($connection, $buffer) {
            // Parse http header.
            list($method, $addr, $http_version) = explode(' ', $buffer);
            $url_data = parse_url($addr);
            $addr = !isset($url_data['port']) ? "{$url_data['host']}:80" : "{$url_data['host']}:{$url_data['port']}";
            // Async TCP connection.
            $remote_connection = new AsyncTcpConnection("tcp://$addr");
            // CONNECT.
            if ($method !== 'CONNECT') {
                $remote_connection->send($buffer);
                // POST GET PUT DELETE etc.
            } else {
                $connection->send("HTTP/1.1 200 Connection Established\r\n\r\n");
            }
            // Pipe.
            $remote_connection->pipe($connection);
            $connection->pipe($remote_connection);
            $remote_connection->connect();
        };
    }

    /**
     * @param $connection
     * @param string $url
     * @throws Exception
     */
    function httpPipe($connection, string $url = 'tcp://www.baidu.com:80')
    {
        $c = new AsyncTcpConnection($url);
        // 设置将当前客户端连接的数据导向80端口的连接
        $c->onConnect = function (AsyncTcpConnection $c) {
            echo "httpPipe connect success\n";
            $c->send("GET / HTTP/1.1\r\nHost: www.baidu.com\r\nConnection: keep-alive\r\n\r\n");
        };
        $c->onMessage = function (AsyncTcpConnection $c, $http_buffer) use ($connection) {
            $connection->send($http_buffer);
        };
        $c->onClose = function (AsyncTcpConnection $c) {
            echo "httpPipe connection closed\n";
        };
        $c->onError = function (AsyncTcpConnection $c, $code, $msg) {
            echo "httpPipe Error code:$code msg:$msg\n";
            $c->close();
        };
        $connection->pipe($c);
        // 设置80端口连接返回的数据导向客户端连接
        $c->pipe($connection);
        // 执行异步连接
        $c->connect();
    }
}