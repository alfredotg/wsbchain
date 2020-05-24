<?php

use PHPUnit\Framework\TestCase;

final class LimitedTableTest extends TestCase
{
  public function testAdd(): void
  {
    $table = new \App\LimitedTable(3);
    $table->push("data1");
    $table->push("data2");
    $table->push("data3");
    $this->assertEquals(["data1", "data2", "data3"], $table->getItems());

    $table->push("data4");
    $table->push("data5");
    $this->assertEquals(["data3", "data4", "data5"], $table->getItems());

    $table->push("data6");
    $this->assertEquals(["data4", "data5", "data6"], $table->getItems());

    for($i = 0; $i<100; $i++)
      $table->push(sprintf("i%d", $i));

    $this->assertEquals(["i97", "i98", "i99"], $table->getItems());
  }
}

