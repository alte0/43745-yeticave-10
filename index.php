<?php
require "helpers.php";
require "data-test.php";

$main = include_template('main.php',
    [
        "categories" => $categories,
        "announcements" => $announcements,
    ]
);

$layout = include_template('layout.php',
    [
        "categories" => $categories,
        "content" => $main,
        "user_name" => $user_name,
        "is_auth" => $is_auth
    ]
);

print($layout);