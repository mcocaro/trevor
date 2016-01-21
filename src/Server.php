<?php

namespace Trevor;

use React\Socket\Server as BaseServer;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;
use Trevor\Handler\HandlerInterface;

class Server extends BaseServer
{
    private $handler;

    public function __construct(LoopInterface $loop, HandlerInterface $handler)
    {
        parent::__construct($loop);
        $this->handler = $handler;

        $this->on('connection', [$this, 'onConnection']);
    }

    public function onConnection(Stream $conn)
    {
        echo "\nConnected: {$conn->getRemoteAddress()}";

        $conn->on('data', [$this, 'onData']);
        $conn->on('end', [$this, 'onEnd']);
    }

    public function onEnd(Stream $conn)
    {
        echo "\nDisconnected client: {$conn->getRemoteAddress()}";
    }

    public function onData($data, Stream $conn)
    {
        $message = msgpack_unpack($data);

        echo "\nRequested method: {$message['method']}";
        echo "\nRequested params: " . print_r($message['params'], true);

        $result = $this->handler->process($message);

        $conn->write($result);
        $conn->end();
    }
}
