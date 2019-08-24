<?php
require "init.php";

$content = '';
$today = date_create('now');

$sqlAnnouncements = 'SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= "$today" GROUP BY id ORDER BY date_create DESC';

$resultAnnouncements = mysqli_query($linkDB, $sqlAnnouncements);

if ($resultCategories && $resultAnnouncements) {
    $error = mysqli_error($linkDB);
    $categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);
    $announcements = mysqli_fetch_all($resultAnnouncements, MYSQLI_ASSOC);

    $content = include_template(
        'main.php',
        [
            "categories" => $categories,
            "announcements" => $announcements,
        ]
    );
} else {
    $error = mysqli_error($linkDB);
    $content = includeErrorTemplate($error);
}

$layout = include_template(
    'layout.php',
    [
        "title" => "Главная - YetiCave",
        "categories" => $categories,
        "content" => $content,
        "user_name" => $user_name,
        "is_auth" => $is_auth
    ]
);

print($layout);