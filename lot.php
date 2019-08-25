<?php
require "init.php";

$content = include_template('404.php');
$title = "Ошибка 404 - YetiCave";

if (!$resultCategories) {
  $error = "Произошлп ошибка в базе данных - " . mysqli_error($linkDB);
  
  showErrorTemplate([
    "title" => "Ошибка БД - YetiCave",
    "error" => $error,
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
  die;
}

$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

if (isset($_GET["id"])) {
  $idLot = intval($_GET["id"]) < 0 ? 0 : intval($_GET["id"]);
  $sqlLot = "SELECT l.id, l.date_completion, l.name, l.start_price, l.image, l.category_id, l.step, l.description, c.name AS category_name, IFNULL(max(b.price), l.start_price) AS price FROM lots l INNER JOIN сategories c ON l.category_id = c.id LEFT JOIN bets b ON l.id = b.lot_id WHERE l.id = $idLot";
  $resultLot = mysqli_query($linkDB, $sqlLot);
  
  if (!$resultLot || mysqli_num_rows($resultLot) === 0) {
    $error = "Произошла ошибка в базе данных - " . mysqli_error($linkDB);

    showErrorTemplate([
      "title" => "Ошибка БД - YetiCave",
      "error" => $error,
      "categories" => $categories,
      "content" => $content,
      "user_name" => $user_name,
      "is_auth" => $is_auth
    ]);
    die;
  }
  
  $lot = mysqli_fetch_all($resultLot, MYSQLI_ASSOC)[0];

  if (isset($lot["id"])) {
    $title = "Лот {$lot["name"]} - YetiCave";
  
    $content = include_template(
      'lot.php',
      [
        "lot" => $lot,
      ]
    );
  }
}

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