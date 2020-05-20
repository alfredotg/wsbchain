<?php

namespace App\Infura;

class W2Wrapper
{
  private $listener;
  private $client;

  function __construct($listener)
  {
    $this->listener = $listener;
  }

  function run($url)
  {
    $this->client = new \WebSocket\Client($url, ["timeout" => 3600, "fragment_size" => 1024*1024]);
    $this->listener->onOpen();
    while(true)
      $this->listener->onData($this->client->receive());
  }

  function send($data)
  {
    $this->client->send($data);
  }
}
