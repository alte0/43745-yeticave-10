<?php
require "init.php";

$categoriesNav = include_template(
    'categories-nav.php',
    [
        "categories" => $categories,
        "categoriesIdCurrent" => $categoriesIdCurrent
    ]
);

if ($isAuth) {
    $seconds = 6;
    header('HTTP/1.0 403 Forbidden', true, 403);
    header("Refresh: $seconds; url=/");
    $error = "Вы уже зарегестрированны на сайте, через $seconds секунд вас перенаправит на главную страницу сайта.";
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
    $user = [
        "email" => !empty($_POST["email"]) ? trim($_POST["email"]) : '',
        "password" => !empty($_POST["password"]) ? trim($_POST["password"]) : '',
        "name" => !empty($_POST["name"]) ? trim($_POST["name"]) : '',
        "message" => !empty($_POST["message"]) ? trim($_POST["message"]) : ''
    ];

    $required = ["email", "password", "name", "message"];

    $rules = [
    "email" => function () use ($user, $linkDB) {
        return validateEmailSignUp($user["email"], $linkDB);
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
            "categoriesNav" => $categoriesNav,
            "errors" => $errors
        ]
    );
} else {
    $content = include_template(
        'sign-up.php',
        [
            "categories" => $categories,
            "categoriesNav" => $categoriesNav
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
        "isAuth" => $isAuth,
        "categoriesNav" => $categoriesNav
    ]
);

print($layout);
