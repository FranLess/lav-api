<?php

namespace App\Websockets\SocketHandler;

use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Exception;

class UpdatePostSocketHandler extends BaseSocketHandler implements MessageComponentInterface
{
    function onMessage(ConnectionInterface $conn, $msg)
    {
    }
}
