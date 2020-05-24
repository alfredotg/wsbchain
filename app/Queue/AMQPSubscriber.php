<?php

namespace App\Queue;

use \PhpAmqpLib\Message\AMQPMessage;

class AMQPSubscriber extends AMQP implements Subscriber
{  
  private $messages = [];

  function subscribe(array $route_keys)
  {
    $channel = $this->channel();
    $exchange = $this->declareExchange($channel); 
    list($queue_name, ,) = $channel->queue_declare("", /*passive*/ false, /*durable*/ false, /*exclusive*/ false, /*auto_delete*/ false);

    foreach($route_keys as $route_key) {
      $channel->queue_bind($queue_name, $exchange, $route_key);
    }
    $channel->basic_consume($queue_name, "", /*no_local*/ false, /*no_ack*/ false, /*exclusive*/ false, /*nowait*/ false, [$this, "onMessage"]);
  }

  function messages(): Iterable
  {
    $channel = $this->channel();
    while($channel->is_consuming()) {
      $channel->wait();
      foreach($this->messages as $amqp_msg)
      {
        $message = unserialize($amqp_msg->body);
        if($message instanceof Message)
          yield $message;
      }
      $this->messages = [];
    }
  }

  function onMessage(AMQPMessage $message)
  {
    $this->messages[] = $message;
  }
}

