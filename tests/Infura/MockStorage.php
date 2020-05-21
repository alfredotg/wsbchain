<?php

namespace Tests\Infura;

class MockStorage implements \App\Infura\Storage
{
  public $transactions = array(); 
  public $blocks = array(); 

  function __construct()
  {
  
  }

  function addTransactions(array $transactions): void
  {
    foreach($transactions as $hash)
      $this->transactions[] = $hash;
  }

  function addBlock(object $block): void
  {
    $this->blocks[] = $block;
  }
}
