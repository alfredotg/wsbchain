<?php


\Co\run(function(){
  $storage = \App\Application::storage();
  $client = new \App\Infura\WsClient(\Conf\Main::INFURA_WS, $storage);
  $client->debug = true;
  try {
    $client->start();
  } catch(Exception $e) {
    printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
  }
});

