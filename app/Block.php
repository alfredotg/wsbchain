<?php

namespace App;

class Block
{
  use StrictAttrs;

  public $hash;
  public $nonce;
  public $number;
  public $size;
  public $count_transactions;
}
