<?php namespace ProcessWire\GraphQL\Type\Fieldtype;

use GraphQL\Type\Definition\CustomScalarType;
use ProcessWire\GraphQL\Cache;
use ProcessWire\GraphQL\Type\Traits\FieldTrait;
use ProcessWire\GraphQL\Type\Traits\InputFieldTrait;
use ProcessWire\GraphQL\Type\Traits\SetValueTrait;

class FieldtypeEmail
{ 
  use FieldTrait;
  use InputFieldTrait;
  use SetValueTrait;

  public static $name = 'Email';

  public static $description = 'E-Mail address in valid format';

  public static function type($field)
  {
    return Cache::type($field->name, function () {
      return new CustomScalarType([
        'name' => self::$name,
        'description' => self::$description,
        'serialize' => function ($value) {
          return (string) $value;
        },
        'parseValue' => function ($value) {
          return (string) $value;
        },
        'parseLiteral' => function ($valueNode) {
          return (string) $valueNode->value;
        },
      ]);
    });
  }
}
