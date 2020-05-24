<?php

namespace App;

use \App\Queue\Subscriber;
use \App\Queue\NewBlockMessage;
use \App\Queue\NewTxMessage;
use \App\Storage\Storage;
use \Swoole\WebSocket\Server as WsServer;
use \Swoole\Table;

class PushServer
{
  const SUBSCRIBE = 3;
  const MAX_EVENTS = 100;

  private $last_events;

  function __construct(Storage $storage, Subscriber $subscriber)
  {
    $this->storage = $storage;
    $this->subscriber = $subscriber;
    $this->last_events = new LimitedTable(self::MAX_EVENTS);
  }

  function bind(WsServer $ws)
  {
    $ws->set([
      'task_worker_num' => 2,
    ]);

    $ws->on('open', function (WsServer $ws, $request) {
      $this->onOpen($ws, $request);
    });

    $ws->on('message', function (WsServer $ws, $frame) {
    });

    $ws->on('close', function (WsServer $ws, $fd) {
    });

    $ws->on('task', function (WsServer $ws, $worker_id, $task_id, $data) {
      $this->onTask($ws, $data);
    });

    $ws->on('start', function(WsServer $ws) {
      $ws->task([self::SUBSCRIBE, null]);
    });
  }

  function onOpen(WsServer $ws, $request)
  {
    foreach($this->last_events->getItems() as $event)
      $this->push($ws, $request->fd, $event);
  }

  function onTask(WsServer $ws, $task)
  {
    list($type, $data) = $task;
    switch($type)
    {
      case self::SUBSCRIBE:
        $this->subscribe($ws);
        break;
    }
  }

  private function push(WsServer $ws, $fd, $data)
  {
    $ws->push($fd, $data);
  }

  private function addEvent(WsServer $ws, $data)
  {
    $data = json_encode($data);
    $this->last_events->push($data);
    foreach($ws->connections as $fd) {
      if($ws->isEstablished($fd)) {
        $this->push($ws, $fd, $data);
      }
    }
  }

  function subscribe(WsServer $ws)
  {
    $this->subscriber->subscribe(["chain.#"]);
    foreach($this->subscriber->messages() as $msg)
    {
      if($msg instanceof NewBlockMessage)
      {
        $block = $this->storage->getBlockByHash($msg->getData());
        if($block)
          $this->addEvent($ws, [$msg->getRoutingKey(), $block]);
      }
      if($msg instanceof NewTxMessage)
      {
        $tx = $this->storage->getTxByHash($msg->getData());
        if($tx)
          $this->addEvent($ws, [$msg->getRoutingKey(), $tx]);
      }
    }
  }
}
