<?php

namespace Tests\Infura;

use App\Block;
use App\Tx;

class MockStorage implements \App\Storage\Storage
{
  public $transactions = array(); 
  public $blocks = array(); 

  function __construct()
  {
  
  }

  function addTransaction(Tx $transaction): void
  {
    $this->transactions[] = $transaction;
  }

  function addBlock(Block $block): void
  {
    $this->blocks[] = $block;
  }

  function getTxByHash(string $hash): Tx
  {
    return null;
  }

  function getBlockByHash(string $hash): Block
  {
    return null;
  }
}
