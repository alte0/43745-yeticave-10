<?php
require "helpers.php";
require "data-test.php";
require "config/php-ini.php";
$dbConf = require_once 'config/db.php';
$categories = [];

$linkDB = mysqli_connect($dbConf["urlDB"], $dbConf["userDB"], $dbConf["passwordDB"], $dbConf["nameDB"]);

if (!$linkDB) {
  $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();

  showErrorTemplateAndDie([
    "error" => $error,
    "categories" => $categories,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
}

mysqli_set_charset($linkDB, "utf8");

$sqlCategories = 'SELECT * FROM сategories';
$resultCategories = mysqli_query($linkDB, $sqlCategories);

if (!$resultCategories) {
  $error = mysqli_error($linkDB);
  showErrorTemplateAndDie([
    "error" => $error,
    "categories" => $categories,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
}

$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);
