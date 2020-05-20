<?php

namespace App;

class Application
{
  static function baseDir()
  {
    return dirname(__DIR__);
  }

  static function storage()
  {
    return new \App\Storage\Redis(\Conf\Main::REDIS_CONN);
  }
}
