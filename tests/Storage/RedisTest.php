<?php

namespace Tests\Storage;

use PHPUnit\Framework\TestCase;
use App\Storage\Redis;
use App\Storage\RedisInCo;

final class RedisTest extends TestCase
{
  use \Tests\MockApiData;

  function testAddHashes()
  {
    $redis = new RedisInCo(new Redis(\Conf\Tests::REDIS_CONN));

    $redis->addTransactions(["tx1", "tx2"]);

    $this->assertEquals(["hash" => "tx1"], $redis->getTxByHash("tx1"));
  }

  function testAddBlock()
  {
    \Co\run(function () {
      $wg = new \Swoole\Coroutine\WaitGroup();
      $redis = new Redis(\Conf\Tests::REDIS_CONN);
      $wg->add();

      $messages = [];
      go(function() use($redis, &$messages, $wg) {
        $iterator = $redis->subscribe([$redis->txChanel(), $redis->blockChanel()]);
        foreach($iterator as $message)
        {
          $messages[] = $message;
          if(count($messages) == 2)
            break;
        }
        $wg->done();
      });

      $block = $this->apiData("block.json");

      $redis2 = new Redis(\Conf\Tests::REDIS_CONN);
      $redis2->addBlock($block->result);
      $redis2->addTransactions($block->result->transactions);

      $wg->wait();
      list($block_msg, $tx_msg) = $messages;

      $data = $block_msg->getData();
      $this->assertEquals("0x2183f640e014f8ad01c963ecc02650d8fd2ab5dccfa65dc4e92f3eead54f0905", $data['hash']);
      $this->assertEquals("0x7db3", $data['size']);

      $data = $tx_msg->getData();
      $this->assertEquals("0x261c78591b9a8035dc40dc58b637fab601d08b4e4d8391b0b387727e37896dcb", $data['hash']);

    });
  }
}
