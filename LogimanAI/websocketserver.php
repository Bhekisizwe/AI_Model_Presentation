<?php
use Ratchet\Server\IoServer;
use UserClasses\BusinessLayer\AIResultsRelay;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__.'/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new AIResultsRelay()
            )
        ),
    8080
    );

$server->run();
?>
