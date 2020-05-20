<?php

namespace App\Storage;

class BlockMessage implements Message
{
  function __construct(string $data)
  {
    $this->data = $data;
  }

  function getName(): string
  {
    return "block";
  }

  function getData()
  {
    return $this->data;
  }
}
