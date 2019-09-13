<?php

namespace ProcessWire\GraphQL\Test;

use PHPUnit\Framework\TestCase;
use ProcessWire\GraphQL\Utils;

abstract class GraphqlTestCase extends TestCase {

  public static $defaultConfig;

  public static function setUpBeforeClass()
  {
    self::$defaultConfig = Utils::module()->data();
  }

  public static function tearDownAfterClass()
  {
    Utils::module()->setArray(self::$defaultConfig);
  }

  public static function execute($payload = null, $variables = null)
  {
  	return Utils::module()->executeGraphQL($payload, $variables);
  }

}