<?php

namespace App\Queue;

class BaseMessage implements Message
{
  function __construct($data)
  {
    $this->data = $data;
  }

  function getRoutingKey(): string
  {
    return (new \ReflectionClass($this))->getShortName();
  }

  function getData()
  {
    return $this->data;
  }
}
