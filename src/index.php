<?php

require __DIR__.'/../vendor/autoload.php';
require 'Chat.php';

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

$loop = React\EventLoop\Factory::create();

$swsa = new ChatClient();
$webSock = new React\Socket\Server('0.0.0.0:9090', $loop);
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            $swsa
        )
    ),
    $webSock
);

function getHtml()
{
    $html = file_get_contents(__DIR__.'/../public/index.html');

    return new \React\Http\Response(
        200,
        array(
            'Content-Type' => 'text/html',
        ),
        $html
    );
}

function getJs()
{
    $js = file_get_contents(__DIR__.'/../public/main.js');

    return new \React\Http\Response(
        200,
        array(
            'Content-Type' => 'text/javascript',
        ),
        $js
    );
}

function writeFile($data, $swsa)
{
    $swsa->responseToClient($data);

    return new \React\Http\Response(
        200,
        array(
            'Content-Type' => 'text/plain',
        ),
        'ok'
    );
}

$server = new Server(function (ServerRequestInterface $request) use ($swsa) {
    $path = $request->getUri()->getPath();
    if ('/' == $path) {
        return getHtml();
    } elseif ('/main.js' == $path) {
        return getJs();
    } elseif ('/order' == $path) {
        return writeFile($request->getParsedBody(), $swsa);
    }
});

$socket = new React\Socket\Server(8080, $loop);
$server->listen($socket);

$loop->run();
