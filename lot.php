<?php
require "init.php";

$content = include_template('404.php');
$title = "Ошибка 404 - YetiCave";

if (!$resultCategories) {
  $error = "Произошлп ошибка в базе данных - " . mysqli_error($linkDB);
  $title = "Ошибка БД - YetiCave";

  $content = includeErrorTemplate($error);
} else {
  $categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);
}

if (isset($_GET["id"])) {
  $idLot = filter_var($_GET["id"], FILTER_VALIDATE_INT) !== false ? filter_var($_GET["id"], FILTER_VALIDATE_INT) : 0;
  $sqlLot = "SELECT l.id, l.date_completion, l.name, l.start_price, l.image, l.category_id, l.step, l.description, c.name AS category_name, IFNULL(max(b.price), l.start_price) AS price FROM lots l INNER JOIN сategories c ON l.category_id = c.id LEFT JOIN bets b ON l.id = b.lot_id WHERE l.id = $idLot";
  $resultLot = mysqli_query($linkDB, $sqlLot);
  
  if (!$resultLot) {
    $error = "Произошла ошибка в базе данных - " . mysqli_error($linkDB);
    $title = "Ошибка БД - YetiCave";

    $content = includeErrorTemplate($error);
  }
  
  $lot = mysqli_fetch_all($resultLot, MYSQLI_ASSOC)[0];

  if (isset($lot["id"])) {
    $title = "Лот  - YetiCave";
  
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
var_dump($idLot);