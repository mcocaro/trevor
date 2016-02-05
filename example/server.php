<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$handler = new Trevor\Handler\DemoHandler();

$socket = new Trevor\Server($loop, $handler);

printf("MsgPackRPC Server listening at port 1337.\n");

$socket->listen(1337, '127.0.0.1');
$loop->run();
