<?php
require "init.php";

if (!$isAuth) {
    $seconds = 6;
    header('HTTP/1.0 403 Forbidden', true, 403);
    header("Refresh: $seconds; url=/");
    $error = "Вы не вошли на сайт, через $seconds секунд вас перенаправит на главную страницу сайта.";
    showErrorTemplateAndDie([
    "categories" => $categories,
    "error" => $error,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesIdCurrent" => $categoriesIdCurrent
  ]);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $lot = [
    "lot-name" => !empty($_POST["lot-name"]) ? trim($_POST["lot-name"]) : '',
    "category" => !empty($_POST["category"]) && is_numeric($_POST["category"]) ? trim($_POST["category"]) : 0,
    "message" => !empty($_POST["message"]) ? trim($_POST["message"]) : '',
    "lot-image" => !empty($_FILES["lot-image"]) ? $_FILES["lot-image"] : [],
    "lot-rate" => !empty($_POST["lot-rate"]) ? trim($_POST["lot-rate"]) : '',
    "lot-step" => !empty($_POST["lot-step"]) ? trim($_POST["lot-step"]) : '',
    "lot-date" => !empty($_POST["lot-date"]) ? trim($_POST["lot-date"]) : ''
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
        if (!isset($errors[$key]) && isset($rules[$key])) {
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

$categoriesNav = include_template(
    'categories-nav.php',
    [
    "categories" => $categories,
    "categoriesIdCurrent" => $categoriesIdCurrent
  ]
);

$layout = include_template(
    'layout.php',
    [
    "title" => "Добавление нового лота - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesNav" => $categoriesNav
  ]
);

print($layout);
