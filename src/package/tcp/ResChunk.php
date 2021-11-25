<?php
namespace php6141\tcp;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Chunk;
use Workerman\Protocols\Http\Response;

class ResChunk
{
    private TcpConnection $conn;
    public function __construct(TcpConnection $conn,string $str)
    {
        $response = new Response(200, array('Transfer-Encoding' => 'chunked'), $str);
        $conn->send($response);
        $this->conn=$conn;
    }

    function send($str = '没有参数'): self
    {
        $this->conn->send(new Chunk($str));
        return $this;
    }

    function close()
    {
        $this->conn->send(new Chunk(''));
    }
}