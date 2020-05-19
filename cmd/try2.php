<?php

use WebSocket\Client;

$client = new Client('wss://mainnet.infura.io/ws/v3/3a3ffd8d44bb481ca7b28bbce3b0ca0b');
$client->send('{"jsonrpc":"2.0","id":1,"method":"eth_subscribe","params":["newHeads"]}');

echo $client->receive(); // Will output 'Hello WebSocket.org!'
