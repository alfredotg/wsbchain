<?php

namespace App\Queue;

interface Message
{
  function getRoutingKey(): string;
  function getData();
}
