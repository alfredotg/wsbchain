<?php

namespace App\Storage;

class BlockMessage implements Message
{
  function __construct(array $data)
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
