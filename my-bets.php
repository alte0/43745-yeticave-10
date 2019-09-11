<?php
require "init.php";

if (!$isAuth) {
    $seconds = 6;
    header('HTTP/1.0 403 Forbidden', true, 403);
    header("Refresh: $seconds; url=/login.php");
    $error = "Вы не можите просматривать эту страницу, так как не вошли на сайт. Через $seconds секунд вас перенаправит на страницу входа.";
    showErrorTemplateAndDie([
    "categories" => $categories,
    "error" => $error,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesIdCurrent" => $categoriesIdCurrent
    ]);
}

$sqlMyBets = "SELECT l.id, l.date_completion, l.name, l.image, l.user_id, l.user_id_winner, c.name AS category_name, b.date_create AS bet_date_create, b.price AS bet_price, u.contacts FROM lots l INNER JOIN сategories c ON l.category_id = c.id LEFT JOIN bets b ON l.id = b.lot_id LEFT JOIN users u ON l.user_id = u.id WHERE b.user_id = $userID ORDER BY bet_date_create DESC";

$resultMyBets = mysqli_query($linkDB, $sqlMyBets);

if (!$resultMyBets || mysqli_num_rows($resultMyBets) === 0) {
    $error = "Произошла ошибка в базе данных - " . mysqli_error($linkDB);

    showErrorTemplateAndDie([
    "error" => $error,
    "categories" => $categories,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesIdCurrent" => $categoriesIdCurrent
    ]);
}

$myBets = mysqli_fetch_all($resultMyBets, MYSQLI_ASSOC);

$categoriesNav = include_template(
    'categories-nav.php',
    [
    "categories" => $categories,
    "categoriesIdCurrent" => $categoriesIdCurrent
    ]
);

$content = include_template(
    'my-bets.php',
    [
    "categories" => $categories,
    "myBets" => $myBets,
    "today" => $today,
    "categoriesNav" => $categoriesNav
    ]
);

$layout = include_template(
    'layout.php',
    [
    "title" => "Мои ставки - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesNav" => $categoriesNav
    ]
);

print($layout);
