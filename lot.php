<?php
require "init.php";

$errors = [];
$isMyLot = false;
$isMyLastBet = false;
$isEndTime = false;

if (isset($_GET["id"])) {
  $lot = getLotById($_GET["id"], $linkDB, ["categories" => $categories, "user_name" => $user_name, "is_auth" => $is_auth]);
  
  $title = "Лот " . clearStrDataTags($lot["name"]) . " - YetiCave";

  $checkRulesBase = ["whose-lot", "whose-last-bet", "date-completion-bet"];

  $rulesBase = [
    "whose-lot" => function () use ($userID, $lot) {
      return validateWhoseLot($userID, $lot["user_id"]);
    },
    "whose-last-bet" => function () use ($userID, $lot) {
      return validateWhoseLastBet($userID, $lot["last_bet_user_id"]);
    },
    "date-completion-bet" => function () use ($lot, $today) {
      return validateDateCompletionBet($lot["date_completion"], $today);
    },
  ];

  foreach ($checkRulesBase as $key) {
    if (isset($rulesBase[$key])) {
      $ruleBase = $rulesBase[$key];
      $errors[$key] = $ruleBase();
    }
  }

  $errors = array_filter($errors);

  if (isset($errors["whose-lot"])) {
    $isMyLot = !$isMyLot;
  }

  if (isset($errors["whose-last-bet"])) {
    $isMyLastBet = !$isMyLastBet;
  }

  if (isset($errors["date-completion-bet"])) {
    $isEndTime = !$isEndTime;
  }
}

if ($is_auth && isset($_GET["id"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
  $bet = [
    "cost" => !empty($_POST["cost"]) ? trim($_POST["cost"]) : ''
  ];

  $required = ["cost"];

  $rules = [
    "cost" => function () use ($lot, $bet) {
      return validateMinBet($lot["price"], $lot["step"], $bet["cost"]);
    }
  ];

  foreach ($required as $key) {
    if (empty($bet[$key])) {
      $errors[$key] = "Это поле нужно заполнить!";
    }
  }

  foreach ($bet as $key => $value) {
    if (!isset($errors[$key]) && isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule();
    }
  }

  $errors = array_filter($errors);

  if (!count($errors)) {
      $sqlBet = "INSERT INTO bets (price, user_id, lot_id) VALUES (?, ?, ?)";
      $stmt = db_get_prepare_stmt($linkDB, $sqlBet, [
        $bet["cost"],
        $userID,
        $lot["id"]
      ]);
      $result = mysqli_stmt_execute($stmt);
  
      if (!$result) {
        $errors["cost"] = "Не удалось добавить ставку! Попробуйте ещё раз";
      }
  
      $lot = getLotById($lot["id"], $linkDB, ["categories" => $categories, "user_name" => $user_name, "is_auth" => $is_auth]);
  }
}

if (isset($lot["id"])) {
  $content = include_template(
    'lot.php',
    [
      "lot" => $lot,
      "is_auth" => $is_auth,
      "isMyLot" => $isMyLot,
      "isMyLastBet" => $isMyLastBet,
      "isEndTime" => $isEndTime,
      "errors" => $errors
    ]
  );
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