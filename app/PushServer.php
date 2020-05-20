<?php

namespace App;

use \App\Storage\Message;
use \Swoole\WebSocket\Server as WsServer;

class PushServer
{
  const PUBLISH = 1;
  const LAST_DATA = 2;

  private $last_events = [];

  function __construct($storage)
  {
    $this->storage = $storage;
  }

  function start($bind_address)
  {
    $ws = new WsServer(parse_url($bind_address, PHP_URL_HOST), parse_url($bind_address, PHP_URL_PORT));
    $ws->set([
      'task_worker_num' => 1,
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

    $ws->after(100, function() use($ws) {
      \go(function() use($ws) {
        $this->subscribe($ws);
      });
    });
    $ws->start();
  }

  function onOpen(WsServer $ws, $request)
  {
    $this->sendTask($ws, self::LAST_DATA, $request->fd);
  }

  function onTask(WsServer $ws, $task)
  {
    list($type, $data) = $task;
    switch($type)
    {
      case self::PUBLISH:
        $this->last_events[] = $data;
        if(count($this->last_events) > 10)
          array_shift($this->last_events);
        foreach($ws->connections as $fd) {
          if($ws->isEstablished($fd)) {
            $this->push($ws, $fd, $data);
          }
        }
        break;
      case self::LAST_DATA:
        $fd = $data;
        foreach($this->last_events as $event)
          $this->push($ws, $fd, $event);
        break;
    }
  }

  private function push(WsServer $ws, $fd, $data)
  {
    $ws->push($fd, json_encode($data));
  }

  private function subscribe(WsServer $ws)
  {
    $this->storage->subscribe([$this->storage->blockChanel(), $this->storage->txChanel()]);
    while($message = $this->storage->recv())
    {
      if($message instanceof Message)
        $this->sendTask($ws, self::PUBLISH, [$message->getName(), $message->getData()]);
    }
  }

  private function sendTask(WsServer $ws, int $type, $data)
  {
    $ws->task([$type, $data]);
  } 
}
