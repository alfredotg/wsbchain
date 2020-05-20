<?php

namespace App\Storage;

class TxMessage implements Message
{
  function __construct(string $data)
  {
    $this->data = $data;
  }

  function getName(): string
  {
    return "tx";
  }

  function getData()
  {
    return $this->data;
  }
}
