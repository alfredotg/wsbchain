<?php

\Co\run(function() {
  $storage = \App\Application::storage();
  $publisher = new \App\Queue\AMQPPublisher(\Conf\Main::AMQP_CONN);
  $client = new \App\Infura\WsClient(\Conf\Main::INFURA_WS, $storage, $publisher);
  $client->debug = true;
  try {
    $client->start();
  } catch(Exception $e) {
    printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
  }
});

