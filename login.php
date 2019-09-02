<?php
require "init.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

} else {
  $content = include_template(
    'login.php',
    [
      "categories" => $categories
    ]
  );
}

$layout = include_template(
  'layout.php',
  [
    "title" => "Вход на сайт - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);
