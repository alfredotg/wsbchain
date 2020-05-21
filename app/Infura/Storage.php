<?php

namespace App\Infura;

interface Storage
{
  function addTransactions(array $transactions): void;
  function addBlock(object $block): void;
}
