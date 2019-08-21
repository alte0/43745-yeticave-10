<?php
require "helpers.php";
require "data-test.php";
$dbConf = require_once 'config/db.php';
$categories = [];
$content = '';

$linkDB = mysqli_connect($dbConf["urlDB"], $dbConf["userDB"], $dbConf["passwordDB"], $dbConf["nameDB"]);
mysqli_set_charset($linkDB, "utf8");

if (!$linkDB) {
    $error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();

    $content = include_template('error.php', ['error' => $error]);
    $layout = include_template(
        'layout.php',
        [
            "title" => "Ошибка - YetiCave",
            "content" => $content
        ]
    );
} else {
    $sqlCategories = 'SELECT * FROM сategories';
    $sqlAnnouncements = 'SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id ORDER BY date_create DESC';

    $resultCategories = mysqli_query($linkDB, $sqlCategories);
    $resultAnnouncements = mysqli_query($linkDB, $sqlAnnouncements);

    if ($resultCategories && $resultAnnouncements) {
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
    } else {
        $error = mysqli_error($linkDB);

        $content = include_template('error.php', ['error' => $error]);
        $layout = include_template(
            'layout.php',
            [
                "title" => "Ошибка - YetiCave",
                "content" => $content
            ]
        );
    }
}

print($layout);
