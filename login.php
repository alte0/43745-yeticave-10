<?php
require "init.php";

if ($is_auth) {
  $seconds = 6;
  header("Refresh: $seconds; url=/");
  $error = "Вы уже зашли на сайт, через $seconds секунд вас перенаправит на главную страницу сайта.";
  showErrorTemplateAndDie([
    "categories" => $categories,
    "error" => $error,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $user = [
    "email" => !empty($_POST["email"]) ? trim($_POST["email"]) : '',
    "password" => !empty($_POST["password"]) ? trim($_POST["password"]) : ''
  ];

  $required = ["email", "password"];

  $rules = [
    "email" => function () use ($user) {
      return validateEmailSignIn($user["email"]);
    },
    "password" => function () use ($user) {
      return validateLength($user['password'], 6, 20);
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
    $email = $user["email"];
    $sqlUser = "SELECT id, email, name, password FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($linkDB, $sqlUser, [$email]);
    mysqli_stmt_execute($stmt);
    $resultUser = mysqli_stmt_get_result($stmt);
    $arr = mysqli_fetch_array($resultUser, MYSQLI_ASSOC);

    if ($arr["email"] === $email && password_verify($saltPwd . $user["password"] . $saltPwd, $arr["password"])) {
        $_SESSION["userInfo"] = [
          "id" => $arr["id"],
          "name" => $arr["name"],
        ];
        header("Location: /");
        die;
    } else {
      $errors["common"] = "Введённый email или пароль - невереный!";
    }
  }

  $content = include_template(
    'login.php',
    [
      "categories" => $categories,
      "errors" => $errors
    ]
  );
} else {
  $content = include_template(
    'login.php',
    [
      "categories" => $categories
    ]
  );
}

$layout = include_template(
  'layout.php',
  [
    "title" => "Вход на сайт - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);