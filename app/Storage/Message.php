<?php

namespace App\Storage;

interface Message
{
  function getName(): string;
  function getData();
}
