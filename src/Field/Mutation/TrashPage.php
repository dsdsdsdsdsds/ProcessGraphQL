<?php namespace ProcessWire\GraphQL\Field\Mutation;

use GraphQL\Type\Definition\Type;
use ProcessWire\GraphQL\Error\ExecutionError;
use ProcessWire\GraphQL\Permissions;
use ProcessWire\GraphQL\Utils;
use ProcessWire\GraphQL\Error\ValidationError;
use ProcessWire\GraphQL\Type\PageType;
use ProcessWire\NullPage;
use ProcessWire\WireException;

class TrashPage
{
  public static function field()
  {
    return [
      'name' => self::name(),
      'description' => self::description(),
      'type' => Type::nonNull(PageType::type()),
      'args' => [
        'id' => Type::nonNull(Type::id()),
      ],
      'resolve' => function ($value, $args) {
        return self::resolve($value, $args);
      }
    ];
  }

  public static function name()
  {
    return 'trash';
  }

  public static function description()
  {
    return 'Move the given page to the Trash.';
  }

  public static function resolve($value, $args)
  {
    // prepare neccessary variables
    $id = Utils::sanitizer()->int($args['id']);
    $page = Utils::pages()->get("id=$id");
    $user = Utils::user();
    $notAllowedMsg = "You ar not allowed to move page `$id` to trash.";

    // check if we got a page
    if ($page instanceof NullPage) {
      throw new ValidationError("Could not find a page `$id`.");
    }

    // make sure the template is legal and trashable by the user
    if (!Permissions::getTrashTemplates()->has($page->template)) {
      throw new ValidationError($notAllowedMsg);
    }

    // if user has no delete permission then check if user
    // has trashCreated permission and has created this page
    $hasDeletePermission = $user->hasPermission(Permissions::pageDeletePermission, $page->template);
    $hasTrashCreatedPermission = $user->hasPermission(Permissions::pageEditTrashCreatedPermission, $page->template);
    $createdThisPage = $page->createdUser->id === $user->id;
    if (
      !$hasDeletePermission &&
      !($hasTrashCreatedPermission && $createdThisPage)
    ) {
        throw new ValidationError($notAllowedMsg);
    }

    // trash the page
    try {
      if (Utils::pages()->trash($page)) {
        return $page;
      }
      throw new WireException("Could not move the page to the trash.");
    } catch (WireException $err) {
      throw new ExecutionError($err->getMessage());
    }
  }
}

