<?php

session_start();
date_default_timezone_set("Europe/Moscow");
require "helpers.php";
require "config/php-ini.php";
$dbConf = require_once 'config/db.php';
$today = date("Y-m-d H:i:s");
$categories = [];
$saltPwd = "S@5s";
$isAuth = isset($_SESSION["userInfo"]);
$user_name = isset($_SESSION["userInfo"]) ? $_SESSION["userInfo"]["name"] : "";
$userID = isset($_SESSION["userInfo"]) ? $_SESSION["userInfo"]["id"] : 0;
$categoriesIdCurrent = 0;
$page_items = 9;
$linkDB = mysqli_connect($dbConf["urlDB"], $dbConf["userDB"], $dbConf["passwordDB"], $dbConf["nameDB"]);

if (!$linkDB) {
    $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();

    showErrorTemplateAndDie([
      "error" => $error,
      "categories" => $categories,
      "user_name" => $user_name,
      "isAuth" => $isAuth
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
      "isAuth" => $isAuth
    ]);
}

$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);
