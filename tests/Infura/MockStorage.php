<?php

namespace Tests\Infura;

class MockStorage implements \App\Infura\Storage
{
  public $block_hashes = array(); 

  function __construct()
  {
  
  }

  function addBlockHash(string $hash): void
  {
    $this->block_hashes[] = $hash;
  }
}
