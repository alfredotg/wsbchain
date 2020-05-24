<?php

use App\Storage\Redis;
use App\Queue\AMQPSubscriber;

$bind_address = \Conf\Main::HTTP_ADDRESS;

$server = new \Swoole\WebSocket\Server(parse_url($bind_address, PHP_URL_HOST), parse_url($bind_address, PHP_URL_PORT));

$http_server = new \App\HttpServer();
$http_server->bind($server);

$subscriber = new AMQPSubscriber(\Conf\Main::AMQP_CONN);
$storage = new Redis(\Conf\Main::REDIS_CONN); 

$push_server = new \App\PushServer($storage, $subscriber);
$push_server->bind($server);
$server->start();

