<?php

use PHPUnit\Framework\TestCase;
use App\StrictAttrs;

final class StrictAttrsTest extends TestCase
{
  public function testSet(): void
  {
    $obj = new class {
      use StrictAttrs;

      public $height;
    };

    $obj->height = 10;
    $this->assertEquals($obj->height, 10);

    $this->expectException(\Exception::class);
    $obj->width = 10;
  }
}

