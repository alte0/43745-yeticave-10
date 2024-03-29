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
    header("Refresh: $seconds; url=/");
    $error = "Вы уже зашли на сайт, через $seconds сек. вас перенаправит на главную страницу сайта.";
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
        "email" => isset($_POST["email"]) && !empty($_POST["email"]) ? trim($_POST["email"]) : '',
        "password" => isset($_POST["password"]) && !empty($_POST["password"]) ? $_POST["password"] : ''
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
        if (!isset($errors[$key]) && isset($rules[$key])) {
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

        if (isset($arr) && password_verify($saltPwd . $user["password"] . $saltPwd, $arr["password"])) {
            $_SESSION["userInfo"] = [
                "id" => $arr["id"],
                "name" => $arr["name"],
            ];
            header("Location: /");
            die;
        } else {
            $errors["common"] = "Вы ввели неверный email/пароль";
        }
    }

    $content = include_template(
        'login.php',
        [
            "categories" => $categories,
            "categoriesNav" => $categoriesNav,
            "errors" => $errors
        ]
    );
} else {
    $content = include_template(
        'login.php',
        [
            "categories" => $categories,
            "categoriesNav" => $categoriesNav
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
        "isAuth" => $isAuth,
        "categoriesNav" => $categoriesNav
    ]
);

print($layout);
