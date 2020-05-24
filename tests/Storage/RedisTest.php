<?php

namespace Tests\Storage;

use PHPUnit\Framework\TestCase;
use App\Storage\Redis;
use App\Storage\RedisInCo;
use App\Tx;
use App\Block;

final class RedisTest extends TestCase
{
  use \Tests\MockApiData;

  function testTx()
  {
    $redis = new RedisInCo(new Redis(\Conf\Tests::REDIS_CONN));

    $tx = new Tx();
    $tx->hash = "0xhash";
    $redis->addTransaction($tx);

    $loaded = $redis->getTxByHash($tx->hash);
    $this->assertEquals($tx->hash, $loaded->hash);
  }

  function testBlock()
  {
    $redis = new RedisInCo(new Redis(\Conf\Tests::REDIS_CONN));

    $block = new Block();
    $block->hash = "0xhash";
    $block->number = 99;
    $redis->addBlock($block);

    $loaded = $redis->getBlockByHash($block->hash);
    $this->assertEquals($block->hash, $loaded->hash);
    $this->assertEquals($block->number, $loaded->number);
  }
}
