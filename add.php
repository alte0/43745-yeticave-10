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
  $lot = $_POST;
  $cats_ids = array_column($categories, 'id');

  $required = ["lot-name", "category", "message", "lot-rate", "lot-step", "lot-date"];

  $rules = [
    "lot-name" => function(){
      return validateLength("lot-name", 10, 255);
    },
    "category" => function () use ($cats_ids) {
      return validateCategory('category', $cats_ids);
    },
    "message" => function () {
      return validateLength("message", 10, 1000);
    },
    "lot-rate" => function () {
      return validateValueOnInteger("lot-rate");
    },
    "lot-step" => function () {
      return validateValueOnInteger("lot-step");
    },
    "lot-date" => function () {
      return validateFormatDateAndPlusMinOne("lot-date");
    }
  ];

  foreach ($_POST as $key => $value) {
    if (isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule(); 
    }
  }
  
  foreach ($required as $key) {
    if (empty($_POST[$key])) {
      $errors[$key] = "Это поле нужно заполнить!";
    }
  }
  
  if (!empty($_FILES['lot-image']['name'])) {
    $errors["file"] = validateFileImageType("lot-image");

    $errors = array_filter($errors);
    
    if (!count($errors)) {
      $uploadDir = 'uploads/';
      $tmp_name = $_FILES['lot-image']['tmp_name'];
      $path = $_FILES['lot-image']['name'];
      $fileType = $_FILES['lot-image']['type'];
      $filename = uniqid() . "." . substr($fileType, 6);
      $filePath = $uploadDir . $filename;
      move_uploaded_file($tmp_name, $filePath);
      $lot['path'] = $filePath;
    }
  } else {
    $errors['file'] = 'Вы не загрузили файл';
  }


  if (count($errors)) {
    $content = include_template(
      'add.php',
      [
        "categories" => $categories,
        "errors" => $errors
      ]
    );
  } else {
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
    }
  }
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