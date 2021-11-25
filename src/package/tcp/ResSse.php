<?php
namespace php6141\tcp;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Response;
use Workerman\Protocols\Http\ServerSentEvents;

class ResSse
{
    private TcpConnection $conn;

    public function __construct(TcpConnection $conn)
    {
        $response = new Response(200, array('Content-Type' => 'text/event-stream'));
        $conn->send($response);
        $this->conn = $conn;
    }

    function send(array $data): self
    {
        $this->conn->send(new ServerSentEvents($data));
        return $this;
    }

}