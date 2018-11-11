<?php

use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class ChatClient implements MessageComponentInterface
{
    protected $clients = [];

    public function __construct()
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
        $conn->send('session_id:'.$conn->resourceId);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function responseToClient($data)
    {
        if ($this->clients[$data['uid']]) {
            $this->clients[$data['uid']]->send('number:'.$data['number']);
        }
    }
}
