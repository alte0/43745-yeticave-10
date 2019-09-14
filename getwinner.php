<?php
require_once "vendor/autoload.php";

$arrData = [
    "categories" => $categories, "user_name" => $user_name, "isAuth" => $isAuth,
    "categoriesIdCurrent" => $categoriesIdCurrent
];

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

if ($lotsWinner = getLotsWithoutWinners($today, $linkDB, $arrData)) {
    foreach ($lotsWinner as $item) {
        setWinner($item["lot_id"], $item["winner_id"], $linkDB, $arrData);

        $message = new Swift_Message();
        $message->setSubject("Ваша ставка победила");
        $message->setFrom(['keks@phpdemo.ru' => 'Интернет Аукцион "YetiCave"']);
        $message->setBcc([$item["email"] => $item["name"]]);

        $msg_content = include_template('email.php', [
        'lotId' => $item["lot_id"],
        'nameUser' => $item["name"],
        'nameLot' => $item["name_lot"]
        ]);
        $message->setBody($msg_content, 'text/html');

        $mailer->send($message);
    }
}
