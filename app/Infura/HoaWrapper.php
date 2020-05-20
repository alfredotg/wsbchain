<?php

namespace App\Infura;

class HoaWrapper
{
  private $listener;
  private $client;

  function __construct($listener)
  {
    $this->listener = $listener;
  }

  function run($url)
  {
    $this->client = new \Hoa\Websocket\Client(
      new \Hoa\Socket\Client($url, 3600)
    );

    $this->client->on('open', function(\Hoa\Event\Bucket $bucket) {
      $this->listener->onOpen();
    });
    $this->client->on('message', function (\Hoa\Event\Bucket $bucket) {
        $data = $bucket->getData();
        if(isset($data['message']))
          $this->listener->onData($data['message']);
    });
    $this->client->setHost(parse_url($url, PHP_URL_HOST));
    $this->client->run();
  }

  function send($data)
  {
    $this->client->send($data, null);
  }
}
