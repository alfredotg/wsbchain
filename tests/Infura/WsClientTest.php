<?php

namespace Tests\Infura;

use PHPUnit\Framework\TestCase;
use App\Infura\WsClient;
use App\Queue\NewBlockMessage;
use App\Queue\NewTxMessage;

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
    $queue = new MockQueue();
    $client = new WsClient("", $storage, $queue);
    $client->setClient($stub);

    $client->onMessage($this->apiData("new_block.json"));

    $data = $this->apiData("block.json");
    $data->id = $client->getCallId();
    $client->onMessage($data);
    $this->assertEquals("0x261c78591b9a8035dc40dc58b637fab601d08b4e4d8391b0b387727e37896dcb", $storage->transactions[0]->hash);
    $block = $storage->blocks[0];
    $this->assertEquals($data->result->hash, $block->hash);

    $msg = array_shift($queue->messages);
    $this->assertTrue($msg instanceof NewBlockMessage);
    $this->assertEquals($data->result->hash, $msg->getData());

    $msg = array_shift($queue->messages);
    $this->assertTrue($msg instanceof NewTxMessage);
    $this->assertEquals("0x261c78591b9a8035dc40dc58b637fab601d08b4e4d8391b0b387727e37896dcb", $msg->getData());
  }
}

