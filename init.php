<?php
require "helpers.php";
require "data-test.php";
require "config/php-ini.php";
$dbConf = require_once 'config/db.php';
$categories = [];

$linkDB = mysqli_connect($dbConf["urlDB"], $dbConf["userDB"], $dbConf["passwordDB"], $dbConf["nameDB"]);

if (!$linkDB) {
  $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();
  $content = include_template('error.php', ['error' => $error]);
  $layout = include_template(
    'layout.php',
    [
      "title" => "Ошибка - YetiCave",
      "content" => $content,
      "user_name" => $user_name,
      "is_auth" => $is_auth
    ]
  );
  print($layout);
  die;
}

mysqli_set_charset($linkDB, "utf8");

$sqlCategories = 'SELECT * FROM сategories';
$resultCategories = mysqli_query($linkDB, $sqlCategories);
