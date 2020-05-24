<?php

namespace App\Queue;

class NewBlockMessage extends BaseMessage
{
  function __construct(string $hash)
  {
    parent::__construct($hash);
  }

  function getRoutingKey(): string
  {
    return "chain.block.new";
  }
}
