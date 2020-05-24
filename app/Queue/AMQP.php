<?php

namespace App\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class AMQP
{
  const EXCHANGE_NAME = 'eth';

  private $conn;
  private $channel = null;
  private $exchange = null;

  function __construct(string $conf)
  {
    $conf = parse_url($conf);
    $this->conn = new AMQPStreamConnection(
      $conf['host'] ?? '127.0.0.1',                                       
      $conf['port'] ?? 5672,                                       
      $conf['user'] ?? 'guest',
      $conf['pass'] ?? 'guest',
      $conf['path'] ?? "/" // vhost  
    );
  } 

  function __destruct()
  {
    if($this->channel)
      $this->channel->close();
    $this->conn->close();
  }

  protected function channel(): AMQPChannel
  {
    if($this->channel == null)
      $this->channel = $this->conn->channel(); 
    return $this->channel;
  }

  protected function declareExchange(AMQPChannel $channel): string
  {
    if($this->exchange === null)
    {
      $channel->exchange_declare(self::EXCHANGE_NAME, AMQPExchangeType::TOPIC, /*passive*/ false, /*durable*/ true, /* auto_delete */ false);
      $this->exchange = self::EXCHANGE_NAME;
    }
    return $this->exchange;
  }
}

