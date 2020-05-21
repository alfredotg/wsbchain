<?php

namespace App\Storage;

use Swoole\Coroutine\Redis as Connection;

class Redis implements \App\Infura\Storage, Subscriber
{
  private $address;
  private $connection;
  private $prefix;

  function __construct(string $url)
  {
    $this->address = [parse_url($url, PHP_URL_HOST), parse_url($url, PHP_URL_PORT)];
    $this->prefix = parse_url($url, PHP_URL_PATH);
  }

  private function connect(): Connection
  {
    $connection = new Connection();  
    if(!$connection->connect($this->address[0], $this->address[1]))
      throw new \Exception('Failed connect to redis');  
    return $connection;
  }

  private function conn(): Connection
  {
    if($this->connection == null)
      $this->connection = $this->connect();  
    return $this->connection;
  }

  function getBlockByHash(string $hash) 
  {
    return $this->getData($this->blockKey($hash));
  }

  function getTxByHash(string $hash) 
  {
    return $this->getData($this->txKey($hash));
  }

  private function getData(string $key)
  {
    $data = null;
    $data = $this->conn()->get($key);
    if($data)
      $data = $this->unserialize($data);
    return $data;
  }

  function addBlock(object $block): void
  {
    $data = [
      "hash" => $block->hash,
      "nonce" => $block->nonce,
      "number" => $block->number,
      "size" => $block->size,
      "count_transactions" => count($block->transactions),
    ];
    $conn = $this->conn();
    $conn->set($this->blockKey($block->hash), $this->serialize($data));
    $conn->publish($this->blockChanel(), $block->hash);
  }

  function addTransactions(array $transactions): void
  {
    $conn = $this->conn();
    foreach($transactions as $hash)
    {
      $conn->set($this->txKey($hash), $this->serialize(["hash" => $hash]));
      $conn->publish($this->txChanel(), $hash);
    }
  }

  function subscribe(array $chanels): Iterable
  {
    $sub_conn = $this->connect();
    $sub_conn->subscribe($chanels);
    while($msg = $sub_conn->recv())
    {
      list($type, $name, $info) = $msg;
      switch($type)
      {
        case 'unsubscribe':
          break;
        case 'message':
          if($name === $this->blockChanel())
            yield new BlockMessage($this->getBlockByHash($info));
          if($name === $this->txChanel())
            yield new TxMessage($this->getTxByHash($info));
      }
    }
  }

  private function blockKey(string $hash): string
  {
    return $this->key("bl", $hash);
  }

  private function txKey(string $hash): string
  {
    return $this->key("tx", $hash);
  }

  function blockChanel(): string
  {
    return $this->key("ch_block");
  }

  function txChanel(): string
  {
    return $this->key("ch_tx");
  }

  private function key(...$key): string
  {
    return $this->prefix . ":" . implode(":", $key);
  }

  private function serialize($data): string
  {
    return json_encode($data);
  }

  private function unserialize(string $data)
  {
    return json_decode($data, true);
  }
}
