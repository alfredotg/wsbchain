<?php

namespace App\Infura;

class WsClient 
{
  public $debug = false;
  private $url;
  private $counter = 0;
  private $storage;
  private $callbacks = [];

  function __construct($url, Storage $storage)
  {
    $this->url = $url;
    $this->storage = $storage;
  }

  public function setClient($client)
  {
    $this->client = $client;
  }

  public function start()
  {
    $this->client = new \WebSocket\Client($this->url);
    $this->onOpen();
    $this->client->setTimeout(3600);
    while(true)
    {
      $message = $this->client->receive();
      if($this->debug)
        printf("IN << %s\n\n", $message);
      $message = json_decode($message);
      if(is_object($message))
        $this->onMessage($message);
      else if($this->debug)
        printf("failed deocde message: %s\n\n", $data['message']);
    }
  }

  public function onMessage(Object $message)
  {
    if(isset($message->method))
    {
      $method = "onResponse";
      foreach(explode("_", $message->method) as $chunk)
        $method .= ucfirst($chunk);
      if(method_exists($this, $method))
      {
        $this->$method($message->params ?? []);
        return;
      }
      assert(false, "Unknown message: " . json_encode($message));
    }
    if(isset($message->id) && isset($this->callbacks[$message->id]))
    {
      $callback = $this->callbacks[$message->id];
      unset($this->callbacks[$message->id]);
      $callback($message->result);
    }
  }

  public function onOpen()
  {
    $this->send("eth_subscribe", "newHeads");
  }

  public function onResponseEthSubscription($data)
  {
    $hash = $data->result->hash;
    $this->storage->addBlockHash($hash);
    $this->send("eth_getBlockByHash", $hash, false);
    $this->onNextResponse(function($data) {
      printf("block!\n");
      var_dump($data);
    });
  }

  public function send($method, ...$params)
  {            
    $this->counter++;
    $data = [
      "jsonrpc" => "2.0",
      "id" => $this->counter,
      "method" => $method,
      "params" => $params
    ];
    if($this->debug)
      printf("OUT >> %s\n\n", json_encode($data));
    $this->client->send(json_encode($data));
  }

  private function onNextResponse(Callable $fn)
  {
    $this->callbacks[$this->counter] = $fn;
  }
}

