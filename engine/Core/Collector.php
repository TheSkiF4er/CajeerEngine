<?php
namespace Core;
class Collector {
  protected static array $data=['sql'=>[],'events'=>[],'templates'=>[],'timers'=>[]];
  public static function push(string $b,$i): void { if(!isset(self::$data[$b])) self::$data[$b]=[]; self::$data[$b][]=$i; }
  public static function all(): array { return self::$data; }
}
