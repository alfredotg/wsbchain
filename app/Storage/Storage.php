<?php

namespace App\Storage;

use App\Block;
use App\Tx;

interface Storage
{
  function addTransaction(Tx $transaction): void;
  function addBlock(Block $block): void;
  function getBlockByHash(string $hash): Block; 
  function getTxByHash(string $hash): Tx; 
}
