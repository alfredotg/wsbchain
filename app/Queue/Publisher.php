<?php

namespace App\Queue;

interface Publisher
{
  function publish(Message $msg);
}
