<?php

namespace App\Storage;

use Swoole\Coroutine\Redis as Connection;

class Redis implements \App\Infura\Storage
{
  private $address;
  private $connection;
  private $prefix;

  function __construct(string $url)
  {
    $this->address = [parse_url($url, PHP_URL_HOST), parse_url($url, PHP_URL_PORT)];
    $this->prefix = parse_url($url, PHP_URL_PATH);
  }

  private function conn(): Connection
  {
    if($this->connection == null)
    {
      $this->connection = new Connection();  
      $this->connection->connect($this->address[0], $this->address[1]);  
    }
    return $this->connection;
  }

  //private function co(Callable $func): void
  //{
  //  $conn = $this->conn();
  //  \Co\run(function() use($conn, $func) {
  //    $func($conn);
  //  });
  //}

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

  function addBlockHash(string $hash): void
  {
    $conn = $this->conn();
    $conn->set($this->blockKey($hash), $this->serialize(["hash" => $hash]));
    $conn->publish($this->blockChanel(), $hash);
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

  function subscribe(array $chanels): void
  {
    $this->conn()->subscribe($chanels);
  }

  function recv()
  {
    while($msg = $this->conn()->recv())
    {
      list($type, $name, $info) = $msg;
      switch($type)
      {
        case 'unsubscribe':
          return null;
        case 'message':
          if($name === $this->blockChanel())
            return new BlockMessage($info);
          if($name === $this->txChanel())
            return new TxMessage($info);
      }
    }
    return null;
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
