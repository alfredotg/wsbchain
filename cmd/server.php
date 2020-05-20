<?php

use \App\Storage\Redis;

$server = new \App\PushServer(new Redis(\Conf\Main::REDIS_CONN));
$server->start(\Conf\Main::WS_ADDRESS);
