<?php
require "init.php";

$errors = [];
$arrData = [
    "categories" => $categories, 
    "user_name" => $user_name, 
    "isAuth" => $isAuth, 
    "categoriesIdCurrent" => $categoriesIdCurrent
];
$isVisibleForm = true;

if (isset($_GET["id"]) && $lot = getLotById($_GET["id"], $linkDB, $arrData)) {
    $bets = getBetsForId($_GET["id"], $linkDB, $arrData);
    $checkRulesBase = ["whose-lot", "whose-last-bet", "date-completion-bet"];

    $rulesBase = [
    "whose-lot" => function () use ($userID, $lot) {
        return checkWhoseLot($userID, $lot["user_id"]);
    },
    "whose-last-bet" => function () use ($userID, $lot) {
        return checkWhoseLastBet($userID, $lot["last_bet_user_id"]);
    },
    "date-completion-bet" => function () use ($lot, $today) {
        return checkDateCompletionBet($lot["date_completion"], $today);
    },
  ];

    foreach ($checkRulesBase as $key) {
        if (isset($rulesBase[$key])) {
            $ruleBase = $rulesBase[$key];
            $errors[$key] = $ruleBase();
        }
    }

    $errors = array_filter($errors);
} else {
    $title = "Ошибка 404 - YetiCave";
    $content = include_template(
        '404.php'
    );
}

if (
    !$isAuth || 
    isset($errors["whose-lot"]) || 
    isset($errors["whose-last-bet"]) || 
    isset($errors["date-completion-bet"])
    ) {
    $isVisibleForm = !$isVisibleForm;
}

if ($isAuth && $_SERVER["REQUEST_METHOD"] === "POST" && $isVisibleForm) {
    $bet = [
        "cost" => !empty($_POST["cost"]) ? trim($_POST["cost"]) : ''
    ];

    $required = ["cost"];

    $rules = [
        "cost" => function () use ($lot, $bet) {
            return checkMinBet($lot["price"], $lot["step"], $bet["cost"]);
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

        $lot = getLotById($_GET["id"], $linkDB, $arrData);
        $bets = getBetsForId($_GET["id"], $linkDB, $arrData);
        $isVisibleForm = !$isVisibleForm;
    }
}

if (isset($lot["id"])) {
    $title = "Лот " . $lot["name"] . " - YetiCave";
    $content = include_template(
        'lot.php',
        [
            "lot" => $lot,
            "bets" => $bets,
            "isAuth" => $isAuth,
            "isVisibleForm" => $isVisibleForm,
            "today" => $today,
            "errors" => $errors
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
        "title" => "Главная - YetiCave",
        "categories" => $categories,
        "content" => $content,
        "user_name" => $user_name,
        "isAuth" => $isAuth,
        "categoriesNav" => $categoriesNav
    ]
);

print($layout);
