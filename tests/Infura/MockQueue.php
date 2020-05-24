<?php

namespace Tests\Infura;

use App\Queue\Message;

class MockQueue implements \App\Queue\Publisher
{
  public $messages = array(); 

  function publish(Message $msg)
  {
    $this->messages[] = $msg;
  }
}
