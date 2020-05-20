<?php

namespace Tests\Infura;

class MockStorage implements \App\Infura\Storage
{
  public $block_hashes = array(); 
  public $transactions = array(); 

  function __construct()
  {
  
  }

  function addBlockHash(string $hash): void
  {
    $this->block_hashes[] = $hash;
  }

  function addTransactions(array $transactions): void
  {
    foreach($transactions as $hash)
      $this->transactions[] = $hash;
  }
}
