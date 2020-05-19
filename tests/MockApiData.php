<?php

namespace Tests;

trait MockApiData
{
  public function apiDataString(string $file): string
  {
    return file_get_contents(\App\App::baseDir() . '/tests/json/' . $file);
  }

  public function apiData(string $file)
  {
    return json_decode($this->apiDataString($file));
  }
}
