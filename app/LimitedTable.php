<?php

namespace App;

class LimitedTable 
{
  private $size;
  private $counter;

  function __construct(int $size)
  {
    $this->counter = new \Swoole\Atomic(0);
    $this->size = $size;
    $table = new \Swoole\Table($size*(1024+8+100));
    $table->column('id', \Swoole\Table::TYPE_INT);
    $table->column('data', \Swoole\Table::TYPE_STRING, 1024);
    $table->create();
    $this->table = $table;
  }

  function push(string $data)
  {
    $id = $this->counter->add(1);
    $this->table->set($id, ['id' => $id, 'data' => $data]);
    if($id > $this->size)
      $this->table->del($id - $this->size);
  }

  function getItems(): array
  {
    $items = [];
    foreach($this->table as $k => $row)
      $items[intval($k)] = $row['data'];
    ksort($items);
    return $items;
  }
}
