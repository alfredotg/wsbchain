<?php

namespace App;

trait StrictAttrs
{
  function __get($name)
  {
    throw new \Exception(sprintf("Attribute %s not found in class %s", $name, __CLASS__));
  }

  function __set($name, $value)
  {
    throw new \Exception(sprintf("Attribute \"%s\" not found in class %s", $name, __CLASS__));
  }
}
