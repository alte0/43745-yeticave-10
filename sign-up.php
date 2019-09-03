<?php
require "init.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $user = [
    "email" => isset($_POST["email"]) ? trim($_POST["email"]) : '',
    "password" => isset($_POST["password"]) ? trim($_POST["password"]) : '',
    "name" => isset($_POST["name"]) ? trim($_POST["name"]) : '',
    "message" => isset($_POST["message"]) ? trim($_POST["message"]) : ''
  ];

  $required = ["email", "password", "name", "message"];

  $rules = [
    "email" => function () use ($user, $linkDB) {
      return validateEmail($user["email"], $linkDB);
    },
    "password" => function () use ($user) {
      return validateLength($user['password'], 6, 20);
    },
    "name" => function () use ($user) {
      return validateLength($user["name"], 3, 20);
    },
    "message" => function () use ($user) {
      return validateLength($user["message"], 10, 255);
    }
  ];

  foreach ($required as $key) {
    if (empty($user[$key])) {
      $errors[$key] = "Это поле нужно заполнить!";
    }
  }

  foreach ($user as $key => $value) {
    if (isset($rules[$key]) && !isset($errors[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule();
    }
  }

  $errors = array_filter($errors);

  if (!count($errors)) {
    if ($hashPwd = password_hash($saltPwd . $user["password"] . $saltPwd, PASSWORD_DEFAULT)) {
      $sqlUser = "INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)";
      $stmt = db_get_prepare_stmt($linkDB, $sqlUser, [
        $user["email"],
        $user["name"],
        $hashPwd,
        $user["message"]
      ]);
      $result = mysqli_stmt_execute($stmt);
  
      if ($result) {
        header("Location: login.php");
        die;
      }
    }
  }

  $content = include_template(
    'sign-up.php',
    [
      "categories" => $categories,
      "errors" => $errors
    ]
  );
} else {
  $content = include_template(
    'sign-up.php',
    [
      "categories" => $categories
    ]
  );
}

$layout = include_template(
  'layout.php',
  [
    "title" => "Регистрация аккаунта - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);
var_dump($errors);