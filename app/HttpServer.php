<?php

namespace App;

use \App\Storage\Message;
use \Swoole\WebSocket\Server as WsServer;

class HttpServer
{                             
  function __construct()
  {
  }

  function bind(WsServer $ws)
  {
    $ws->on('request', [$this, 'handle']);
  }

  function handle(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
  {
    switch($request->server["path_info"])
    {
      case "/":
        $response->end(file_get_contents(Application::baseDir() . '/public/index.html'));
        break;
      case "/app.js":
        $response->end(file_get_contents(Application::baseDir() . '/public/app.js'));
        break;
    }
  }
}
