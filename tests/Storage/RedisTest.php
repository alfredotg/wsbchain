<?php

namespace Tests\Storage;

use PHPUnit\Framework\TestCase;
use App\Storage\Redis;
use App\Storage\RedisInCo;

final class RedisTest extends TestCase
{
  function testAddBlock()
  {
    $redis = new RedisInCo(new Redis(\Conf\Tests::REDIS_CONN));
    $redis->addBlockHash("my_block_hash");

    $this->assertEquals(["hash" => "my_block_hash"], $redis->getBlockByHash("my_block_hash"));

    $redis->addTransactions(["tx1", "tx2"]);

    $this->assertEquals(["hash" => "tx1"], $redis->getTxByHash("tx1"));
  }
}
