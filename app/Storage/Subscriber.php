<?php

namespace App\Storage;

interface Subscriber {
  function txChanel();
  function blockChanel();
  function subscribe(array $chanels): Iterable;
}
