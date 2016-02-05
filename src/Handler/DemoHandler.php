<?php

namespace Trevor\Handler;

class DemoHandler implements HandlerInterface
{
    public function process(array $message)
    {
        printf("Received message: %s\n", print_r($message, true));

        // Do some stuff here

        return msgpack_pack(['result' => true]);
    }
}
