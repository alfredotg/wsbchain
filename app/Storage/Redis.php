<?php

namespace App\Storage;

use App\Block;
use App\Tx;

class Redis implements Storage
{
  private $con;
  private $prefix;

  function __construct(string $url)
  {
    $this->conn = new \Predis\Client([
      'scheme' => 'tcp',
      'host'   => parse_url($url, PHP_URL_HOST),
      'port'   => parse_url($url, PHP_URL_PORT),
    ]);
    $this->prefix = parse_url($url, PHP_URL_PATH);
  }

  function getBlockByHash(string $hash): Block 
  {
    return $this->getData($this->blockKey($hash));
  }

  function getTxByHash(string $hash): Tx 
  {
    return $this->getData($this->txKey($hash));
  }

  private function getData(string $key)
  {
    $data = null;
    $data = $this->conn->get($key);
    if(!$data)
      return null;
    return $this->unserialize($data);
  }

  function addBlock(Block $block): void
  {
    $this->conn->set($this->blockKey($block->hash), $this->serialize($block));
  }

  function addTransaction(Tx $tx): void
  {
    $this->conn->set($this->txKey($tx->hash), $this->serialize($tx));
  }

  private function blockKey(string $hash): string
  {
    return $this->key("bl", $hash);
  }

  private function txKey(string $hash): string
  {
    return $this->key("tx", $hash);
  }

  private function key(...$key): string
  {
    return $this->prefix . ":" . implode(":", $key);
  }

  private function serialize($data): string
  {
    return serialize($data);
  }

  private function unserialize(string $data)
  {
    return unserialize($data);
  }
}
