<?php

namespace App\Queue;

class NewTxMessage extends BaseMessage
{
  function __construct(string $hash)
  {
    parent::__construct($hash);
  }

  function getRoutingKey(): string
  {
    return "chain.tx.new";
  }
}
