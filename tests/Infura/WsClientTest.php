<?php

namespace Tests\Infura;

use PHPUnit\Framework\TestCase;
use App\Infura\WsClient;

final class WsClientTest extends TestCase
{
  use \Tests\MockApiData;

  public function testTrue(): void
  {
    $stub = $this->createMock(\Hoa\Websocket\Client::class);
    $stub->method('on');
    $stub->method('setHost');
    $stub->method('run');
    $stub->method('send');

    $storage = new MockStorage();
    $client = new WsClient("", $storage);
    $client->setClient($stub);

    $client->onMessage($this->apiData("new_block.json"));
    $this->assertEquals($storage->block_hashes, ["0x7b136f3b1c0d4ca8309431b081abf2cac9ddd7a088b1bd332e795aae3d9bcddf"]);
  }
}

