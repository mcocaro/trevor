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
        printf("Connected: %s\n", $conn->getRemoteAddress());

        $conn->on('data', [$this, 'onData']);
        $conn->on('end', [$this, 'onEnd']);
    }

    public function onEnd(Stream $conn)
    {
        printf("Disconnected client: %s\n", $conn->getRemoteAddress());
    }

    public function onData($data, Stream $conn)
    {
        $message = msgpack_unpack($data);

        printf("Request method: %s\n", $message['method']);
        printf("Request params: %s\n", print_r($message['params'], true));

        $result = $this->handler->process($message);

        $conn->write($result);
        $conn->end();
    }
}
