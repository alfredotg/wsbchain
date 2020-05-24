<?php

namespace App\Queue;

use PhpAmqpLib\Message\AMQPMessage;

class AMQPPublisher extends AMQP implements Publisher
{
  function publish(Message $msg)
  {
    $channel = $this->channel();
    $exchange = $this->declareExchange($channel); 
    $amqp_msg = new AMQPMessage(serialize($msg));
    $channel->basic_publish($amqp_msg, $exchange, $msg->getRoutingKey());
  }
}

