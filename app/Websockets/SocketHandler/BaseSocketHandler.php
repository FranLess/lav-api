<?php

namespace App\Websockets\SocketHandler;


class BaseSocketHandler implements \Ratchet\MessageComponentInterface
{
    public function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        # code...
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        # code...
    }

    public function onClose(\Ratchet\ConnectionInterface $conn)
    {
        # code...
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg)
    {
        # code...
    }
}
