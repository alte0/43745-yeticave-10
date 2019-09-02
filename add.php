<?php
require "init.php";

if (!$is_auth) {
  $seconds = 6;
  header("Refresh: $seconds; url=/");
  $error = "Вы не вошли на сайт, через $seconds секунд вас перенаправит на главную страницу сайта.";
  showErrorTemplateAndDie([
    "categories" => $categories,
    "error" => $error,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $lot = [
    "lot-name" => isset($_POST["lot-name"]) ? trim($_POST["lot-name"]) : '',
    "category" => isset($_POST["category"]) && is_numeric($_POST["category"]) ? trim($_POST["category"]) : 0,
    "message" => isset($_POST["message"]) ? trim($_POST["message"]) : '',
    "lot-image" => isset($_FILES["lot-image"]) ? $_FILES["lot-image"] : [],
    "lot-rate" => isset($_POST["lot-rate"]) ? trim($_POST["lot-rate"]) : '',
    "lot-step" => isset($_POST["lot-step"]) ? trim($_POST["lot-step"]) : '',
    "lot-date" => isset($_POST["lot-date"]) ? trim($_POST["lot-date"]) : ''
  ];

  $required = ["lot-name", "category", "message", "lot-rate", "lot-step", "lot-date"];

  $rules = [
    "lot-name" => function () use ($lot) {
      return validateLength($lot["lot-name"], 10, 255);
    },
    "category" => function () use ($lot, $linkDB) {
      return validateCategory($lot['category'], $linkDB);
    },
    "message" => function () use ($lot) {
      return validateLength($lot["message"], 10, 1000);
    },
    "lot-image" => function () use ($lot) {
      return validateFileAndTypeImage($lot["lot-image"]);
    },
    "lot-rate" => function () use ($lot) {
      return validateValueOnInteger($lot["lot-rate"]);
    },
    "lot-step" => function () use ($lot) {
      return validateValueOnInteger($lot["lot-step"]);
    },
    "lot-date" => function () use ($lot) {
      return validateFormatDateAndPlusMinOne($lot["lot-date"]);
    }
  ];

  foreach ($required as $key) {
    if (empty($lot[$key]) || $lot[$key] === "Выберите категорию") {
      $errors[$key] = "Это поле нужно заполнить!";
    }
  }

  foreach ($lot as $key => $value) {
    if (isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule();
    }
  }

  $errors = array_filter($errors);

  if (!count($errors)) {
    $uploadDir = 'uploads/';
    $tmp_name = $lot['lot-image']['tmp_name'];
    $path = $lot['lot-image']['name'];
    $filename = uniqid() . "." . substr(mime_content_type($tmp_name), 6);
    $filePath = $uploadDir . $filename;
    move_uploaded_file($tmp_name, $filePath);
    $lot['path'] = $filePath;

    $sqlLot = "INSERT INTO lots (name, description, start_price, image, step, date_completion, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($linkDB, $sqlLot, [
      $lot["lot-name"],
      $lot["message"],
      $lot["lot-rate"],
      $lot["path"],
      $lot["lot-step"],
      $lot["lot-date"],
      $userID,
      $lot["category"]
    ]);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      $lot_id = mysqli_insert_id($linkDB);

      header("Location: lot.php?id=" . $lot_id);
      die;
    }
  }

  $content = include_template(
    'add.php',
    [
      "categories" => $categories,
      "errors" => $errors
    ]
  );
} else {
  $content = include_template(
    'add.php',
    [
      "categories" => $categories
    ]
  );
}

$layout = include_template(
  'layout.php',
  [
    "title" => "Добавить лот - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);
