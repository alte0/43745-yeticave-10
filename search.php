<?php
require "init.php";

if (isset($_GET["search"]) && !empty($_GET["search"])) {
    $searchText = trim($_GET["search"]);
    $cur_page = isset($_GET['page']) && !empty($_GET['page']) ? intval($_GET['page']) : 1;

    $sqlSearchCount = "SELECT COUNT(*) as count FROM (SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= '$today' and MATCH(lots.name, lots.description) AGAINST(?) GROUP BY id ORDER BY date_create DESC) AS t";

    $stmtCount = db_get_prepare_stmt($linkDB, $sqlSearchCount, [$searchText]);
    mysqli_stmt_execute($stmtCount);
    $resultSearchCount = mysqli_stmt_get_result($stmtCount);

    if (!$resultSearchCount) {
        $error = mysqli_error($linkDB);
        showErrorTemplateAndDie([
            "error" => $error,
            "categories" => $categories,
            "content" => $content,
            "user_name" => $user_name,
            "isAuth" => $isAuth,
            "categoriesIdCurrent" => $categoriesIdCurrent
        ]);
    }

    $items_count = mysqli_fetch_array($resultSearchCount)["count"];

    $pages_count = ceil($items_count / $page_items);
    if ($cur_page > $pages_count) {
        $cur_page = $pages_count;
    }
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    $sqlSearch = "SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= '$today' and MATCH(lots.name, lots.description) AGAINST(?) GROUP BY id ORDER BY date_create DESC LIMIT " . $page_items . ' OFFSET ' . $offset;

    $stmt = db_get_prepare_stmt($linkDB, $sqlSearch, [$searchText]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($linkDB);
        showErrorTemplateAndDie([
            "error" => $error,
            "categories" => $categories,
            "content" => $content,
            "user_name" => $user_name,
            "isAuth" => $isAuth,
            "categoriesIdCurrent" => $categoriesIdCurrent
        ]);
    }

    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = "Не задан текст для поиска!";
    showErrorTemplateAndDie([
        "categories" => $categories,
        "error" => $error,
        "user_name" => $user_name,
        "isAuth" => $isAuth,
        "categoriesIdCurrent" => $categoriesIdCurrent
    ]);
}

$categoriesNav = include_template(
    'categories-nav.php',
    [
        "categories" => $categories,
        "categoriesIdCurrent" => $categoriesIdCurrent,
    ]
);

$content = include_template(
    'search.php',
    [
        "categories" => $categories,
        "searchText" => $searchText,
        "lots" => $lots,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'cur_page' => $cur_page,
        'page_items' => $page_items,
        "categoriesNav" => $categoriesNav
    ]
);

$layout = include_template(
    'layout.php',
    [
        "title" => "Результаты поиска - YetiCave",
        "categories" => $categories,
        "content" => $content,
        "user_name" => $user_name,
        "isAuth" => $isAuth,
        "categoriesNav" => $categoriesNav
    ]
);

print($layout);
