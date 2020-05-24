<?php

namespace Tests\Queue;

use PHPUnit\Framework\TestCase;
use App\Queue\BaseMessage;
use App\Queue\AMQPPublisher;
use App\Queue\NewTxMessage;
use App\Queue\AMQPSubscriber;

final class AMQPTest extends TestCase
{
  function testSubscribe()
  {
    $subscriber = new AMQPSubscriber(\Conf\Tests::AMQP_CONN);
    $subscriber->subscribe(["chain.#"]);

    $publisher = new AMQPPublisher(\Conf\Tests::AMQP_CONN);
    $publisher->publish(new NewTxMessage("my_hash"));

    $msg = null;
    foreach($subscriber->messages() as $msg)
      break;
    $this->assertTrue($msg instanceof NewTxMessage);
    $this->assertEquals("my_hash", $msg->getData());
  }
}
