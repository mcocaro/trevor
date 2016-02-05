<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dns = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$connector = new React\SocketClient\Connector($loop, $dns);

$connector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
    $data = msgpack_pack([
        'method' => 'doSomeStuff',
        'params' => [
            'say' => 'Hello world!'
        ]
    ]);

    $stream->write($data);

    $stream->on('data', function ($data) {
        $msg = msgpack_unpack($data);
        echo "\nData: " . print_r($msg, true);
    });
});

$loop->run();
