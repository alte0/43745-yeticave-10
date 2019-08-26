<?php
require "init.php";

$title = "Добавить лот - YetiCave";

$content = include_template(
  'add.php',
  [
    "categories" => $categories
  ]
);

$layout = include_template(
  'layout.php',
  [
    "title" => $title,
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);