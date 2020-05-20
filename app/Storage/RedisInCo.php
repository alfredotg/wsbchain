<?php

namespace App\Storage;

class RedisInCo
{
  private $redis;

  function __construct(Redis $redis)
  {
    $this->redis = $redis;
  }

  function __call($method, $args)
  {
    $resutl = null;
    \Co\run(function () use(&$resutl, $method, $args) {
      $resutl = call_user_func_array([$this->redis, $method], $args); 
    });
    return $resutl;
  }
}
