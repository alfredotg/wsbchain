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

  public function getCallId(): int
  {
    return $this->counter;
  }

  public function setClient($client)
  {
    $this->client = $client;
  }

  public function start()
  {
    $this->client = new W2Wrapper($this);
    $this->client->run($this->url);
  }

  public function onData(string $data): void
  {
    if($this->debug)
      printf("IN << %s\n\n", substr(json_encode($data), 0, 100));
    $message = json_decode($data);
    if(is_object($message))
      $this->onMessage($message);
    else if($this->debug)
      printf("failed deocde message: %s\n\n", $data);
  }

  public function onMessage(Object $message): void
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

  public function onOpen(): void
  {
    $this->send("eth_subscribe", "newHeads");
  }

  public function onResponseEthSubscription(Object $data): void
  {
    $hash = $data->result->hash;
    $this->send("eth_getBlockByHash", $hash, false);
    $this->onNextResponse(function($data) {
      if(!is_object($data))
        return;
      if($this->debug)
        printf("new block %s, transactions: %d\n", $data->hash, count($data->transactions));
      $this->storage->addTransactions($data->transactions);
      $this->storage->addBlock($data);
    });
  }

  public function send(string $method, ...$params): void
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

