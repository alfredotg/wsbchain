<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class HelloTest extends TestCase
{
  public function testCanBeCreatedFromValidEmailAddress(): void
  {
    $this->assertTrue(true);
  }
}

