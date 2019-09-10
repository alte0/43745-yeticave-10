<?php
require "init.php";

if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET["search"]) && !empty($_GET["search"])) {
  $searchText = trim($_GET["search"]);
  $cur_page = $_GET['page'] ?? 1;
  $page_items = 9;

  $sqlSearchCount = "SELECT COUNT(*) as count FROM (SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= '$today' and MATCH(lots.name, lots.description) AGAINST('Коллекция') GROUP BY id ORDER BY date_create DESC) AS t";

  $resultSearchCount = mysqli_query($linkDB, $sqlSearchCount);
  $items_count = mysqli_fetch_array($resultSearchCount)["count"];

  $pages_count = ceil($items_count / $page_items);
  $offset = ($cur_page - 1) * $page_items;

  $pages = range(1, $pages_count);
  
  $sqlSearch = "SELECT lots.*, c.name AS category FROM lots INNER JOIN сategories c ON lots.category_id = c.id LEFT JOIN bets b ON lots.id = b.lot_id WHERE lots.date_completion >= '$today' and MATCH(lots.name, lots.description) AGAINST(?) GROUP BY id ORDER BY date_create DESC LIMIT " . $page_items . ' OFFSET ' . $offset;

  $stmt = db_get_prepare_stmt($linkDB, $sqlSearch, [$searchText]);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

  $title = "Результаты поиска - YetiCave";

  $content = include_template(
    'search.php',
    [
      "categories" => $categories,
      "searchText" => $searchText,
      "lots" => $lots,
      'pages' => $pages,
      'pages_count' => $pages_count,
      'cur_page' => $cur_page,
      'page_items' => $page_items
    ]
  );
} else {
  $error = "Не задан текст для поиска!";
  showErrorTemplateAndDie([
    "categories" => $categories,
    "error" => $error,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]);
}

$layout = include_template(
  'layout.php',
  [
    "title" => $title,
    "categories" => $categories,
    "content" => $content,
    "user_name" => $user_name,
    "is_auth" => $is_auth
  ]
);

print($layout);
