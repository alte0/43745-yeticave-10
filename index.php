<?php
require "helpers.php";
require "data-test.php";
$dbConf = require_once 'config/db.php';
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$categories = [];
$content = '';
$today = date_create('now');

$linkDB = mysqli_connect($dbConf["urlDB"], $dbConf["userDB"], $dbConf["passwordDB"], $dbConf["nameDB"]);

if (!$linkDB) {
    $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();
    showErrorTemplate([
        "categories" => $categories, 
        "error" => $error,
        "user_name" => $user_name,
        "is_auth" => $is_auth
        ]);
    die;
}

mysqli_set_charset($linkDB, "utf8");

$sqlCategories = 'SELECT * FROM сategories';
$sqlAnnouncements = 'SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= "$today" GROUP BY id ORDER BY date_create DESC';

$resultCategories = mysqli_query($linkDB, $sqlCategories);
$resultAnnouncements = mysqli_query($linkDB, $sqlAnnouncements);

if (!$resultCategories && !$resultAnnouncements) {
    $error = mysqli_error($linkDB);
    showErrorTemplate([
        "categories" => $categories,
        "error" => $error,
        "user_name" => $user_name,
        "is_auth" => $is_auth
    ]);
    die;
}

$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);
$announcements = mysqli_fetch_all($resultAnnouncements, MYSQLI_ASSOC);

$main = include_template(
    'main.php',
    [
        "categories" => $categories,
        "announcements" => $announcements,
    ]
);

$layout = include_template(
    'layout.php',
    [
        "title" => "Главная - YetiCave",
        "categories" => $categories,
        "content" => $main,
        "user_name" => $user_name,
        "is_auth" => $is_auth
    ]
);

print($layout);
var_dump(date_format($today, 'Y-m-d H:i:s'));