<?php

$storage = new \Tests\Infura\MockStorage;
$client = new \App\Infura\WsClient('wss://mainnet.infura.io/ws/v3/3a3ffd8d44bb481ca7b28bbce3b0ca0b', $storage);
$client->debug = true;
$client->start();

