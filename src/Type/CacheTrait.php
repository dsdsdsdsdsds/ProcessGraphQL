<?php namespace ProcessWire\GraphQL\Type;

use ProcessWire\Template;

trait CacheTrait
{
  private static $store = [];
  public static function cache($key = 'default', $build)
  {
    if (isset(self::$store[$key])) {
      return self::$store[$key];
    }

    self::$store[$key] = $build();
    return self::$store[$key];
  }
}
