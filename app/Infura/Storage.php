<?php

namespace App\Infura;

interface Storage
{
  function addBlockHash(string $hash): void;
}
