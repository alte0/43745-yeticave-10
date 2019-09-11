<?php
require "init.php";

$arrData = [
  "categories" => $categories, "user_name" => $user_name, "isAuth" => $isAuth,
  "categoriesIdCurrent" => $categoriesIdCurrent
];

if (isset($_GET["id"]) && is_numeric($_GET["id"]) && $categoryName = getCategoryName($_GET["id"], $linkDB, $arrData)) {
    $searchCategory = intval(trim($_GET["id"]));
    $categoriesIdCurrent = $searchCategory;
    $cur_page = $_GET['page'] ?? 1;

    $sqlSearchCount = "SELECT COUNT(*) as count FROM (SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= ? AND lots.category_id = ?) AS t";

    $stmtCount = db_get_prepare_stmt($linkDB, $sqlSearchCount, [$today, $searchCategory]);
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
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    $sqlSearchCategoryId = "SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= ? AND lots.category_id = ? ORDER BY lots.date_create DESC LIMIT " . $page_items . " OFFSET " . $offset;

    $stmt = db_get_prepare_stmt($linkDB, $sqlSearchCategoryId, [$today, $searchCategory]);
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
    $error = "Не найдена категория!";
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
    "categoriesIdCurrent" => $categoriesIdCurrent
  ]
);

$content = include_template(
    'all-lots.php',
    [
    "categories" => $categories,
    "lots" => $lots,
    "searchCategory" => $searchCategory,
    'pages' => $pages,
    'pages_count' => $pages_count,
    'cur_page' => $cur_page,
    'page_items' => $page_items,
    "categoriesNav" => $categoriesNav,
    "categoryName" => $categoryName
  ]
);

$layout = include_template(
    'layout.php',
    [
    "title" => "Все лоты категории $categoriesIdCurrent - YetiCave",
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "isAuth" => $isAuth,
    "categoriesNav" => $categoriesNav
  ]
);

print($layout);
