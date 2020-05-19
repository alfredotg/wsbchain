<?php

require_once(__DIR__ . '/vendor/autoload.php');

$cmd = $argv[1] ?? null;
$dir = realpath(__DIR__ . '/cmd'); 
$file = realpath($dir . '/' . $cmd . '.php');
if(!$file || !is_file($file) || strpos($file, $dir) !== 0)
{
  printf("Command \"%s\" not found\n", $cmd);
  printf("Avaible commands:\n");
  foreach(glob($dir . '/*.php') as $file)
  {
    printf("    %s\n", basename($file, ".php"));
  }
  exit(1);
}

include($file);
