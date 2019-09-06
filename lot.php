<?php
require "init.php";

$lot = getLotById($_GET["id"], $linkDB, ["categories" => $categories, "user_name" => $user_name, "is_auth" => $is_auth]);

if ($is_auth && isset($_GET["id"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
  $errors = [];

  $minBet = $lot["price"] + $lot["step"];

  $cost = !empty($_POST["cost"]) ? trim($_POST["cost"]) : '';

  if ($cost >= $minBet) {
    $sqlBet = "INSERT INTO bets (price, user_id, lot_id) VALUES (?, ?, ?)";
    $stmt = db_get_prepare_stmt($linkDB, $sqlBet, [
      $cost,
      $userID,
      $lot["id"]
    ]);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
      $errors["cost"] = "Не удалось добавить ставку! Попробуйте ещё раз";
    }

    $lot = getLotById($lot["id"], $linkDB, ["categories" => $categories, "user_name" => $user_name, "is_auth" => $is_auth]);
  } else {
    $errors["cost"] = "Ваша ставка меньше минимальной ставки!";
  }

  $title = "Лот {$lot["name"]} - YetiCave";

  $content = include_template(
    'lot.php',
    [
      "lot" => $lot,
      "is_auth" => $is_auth,
      "errors" => $errors
    ]
  );

} elseif (isset($_GET["id"])) {
  if (isset($lot["id"])) {
    $title = "Лот {$lot["name"]} - YetiCave";
  
    $content = include_template(
      'lot.php',
      [
        "lot" => $lot,
        "is_auth" => $is_auth
      ]
    );
  }
}

$layout = include_template(
  'layout.php',
  [
    "title" => $title ?? "Ошибка 404 - YetiCave",
    "categories" => $categories,
    "content" => $content ?? include_template('404.php'),
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);