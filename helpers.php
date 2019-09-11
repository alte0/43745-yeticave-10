<?php

use phpDocumentor\Reflection\Types\Integer;

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        var_dump($data);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Функция форматирования суммы и добавления к ней знака рубля
 * @param float $num Форматируемая сумма
 * @param boolean $isAddRubleSign Добовлять ли знак рубля
 * @return string отформатированная сумма вместе со знаком рубля
 */
function formatPrice(float $num, $isAddRubleSign = true): string
{
    $num = ceil($num);
    if ($num >= 1000) {
        $num = number_format($num, 0, '.', ' ');
    }

    return $isAddRubleSign ? $num . '<b class="rub">р</b>' : $num;
}

/**
 * Функция очистки данных от тэгов
 * @param string $num Очишаемая строка
 * @return string Очишенная строка
 */
function clearStrDataTags($str): string
{
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция добаления 0-ей до 2-х знаков
 * @param string $str
 * @return string
 */
function addStrPadZero($str): string
{
    return str_pad($str, 2, "0", STR_PAD_LEFT);
}
/**
 * Функция вычисления оставшегося времени в формате «ЧЧ:ММ»
 * @param date $date дата в формате ГГГГ-ММ-ДД;
 * @return array [09, 29] - «ЧЧ:ММ»
 */
function calcDateExpiration($date): array
{
    if (strtotime('now') > strtotime($date)) {
        return [
            "hours" => "00",
            "minutes" => "00",
            "seconds" => "00"
        ];
    }

    $dateEnd = date_create($date);
    $dateNow = date_create('now');
    $dateDiff = date_diff($dateEnd, $dateNow);
    $timeLeftStr = date_interval_format($dateDiff, "%d %H %I %s");
    $timeLeft = explode(" ", $timeLeftStr);
    $hours = addStrPadZero($timeLeft[0] * 24 + $timeLeft[1]);
    $minutes = addStrPadZero($timeLeft[2]);
    $seconds = addStrPadZero($timeLeft[3]);

    return [
        "hours" => $hours,
        "minutes" => $minutes,
        "seconds" => $seconds
    ];
}
/**
 * Функция показывает шаблон с ошибокой
 * @param array $data - ассоциативный массив для передачи данных;
 */
function showErrorTemplateAndDie(array $data)
{
    extract($data);
    $categoriesNav = include_template(
        'categories-nav.php',
        [
        "categories" => $categories,
        "categoriesIdCurrent" => $categoriesIdCurrent
        ]
    );
    $content = include_template('error.php', ['error' => $error]);
    $layout = include_template(
        'layout.php',
        [
        "title" => "Ошибка - YetiCave",
        "categories" => $categories,
        "content" => $content,
        "user_name" => $user_name,
        "isAuth" => $isAuth,
        "categoriesNav" => $categoriesNav
        ]
    );
    print($layout);
    die;
}
/**
 * Получения значения из $_POST для заполнеиня данных в форме.
 * @param string $name - имя ключа из массива $_POST для получения значения;
 * @return void
 */
function getPostVal($name)
{
    // htmlentities для сохранения кавычек
    return isset($_POST[$name]) ? htmlentities($_POST[$name]) : "";
}
/**
 * Валидация строки
 * @param string $value - значение для валидации;
 * @param integer $min - минимальное значение длины строки;
 * @param integer $max - максимальное значение длины строки;
 */
function validateLength(string $value, int $min, int $max)
{
    $len = strlen($value);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}
/**
 * Валидация id категории из масива
 * @param string $value - значение для валидации;
 * @param resource $link - соединение с БД;
 */
function validateCategory($value, $link)
{
    if (mysqli_connect_errno()) {
        return "Не удалось проверить категорию";
    }

    $sql = "SELECT id FROM сategories WHERE id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$value]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $arr = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($arr["id"] !== intval($value)) {
        return "Указана несуществующая категория";
    }

    return null;
}
/**
 * Валидация целого числа и и больше нуля
 * @param string $value - значение для валидации;
 */
function validateValueOnInteger($num)
{
    if (!(is_numeric($num) && $num > 0)) {
        return "Значение должно быть целым числом и больше нуля";
    }

    return null;
}
/**
 * Валидация даты на формат и дата больше текущей даты, хотя бы на один день
 * @param string $date - значение для валидации;
 */
function validateFormatDateAndPlusMinOne($date)
{
    $today = date("Y-m-d");

    if (!(is_date_valid($date) && strtotime($date) >= strtotime($today) + 86400)) {
        return "Введите дату завершения торгов в формате ГГГГ-ММ-ДД + один день";
    }

    return null;
}
/**
 * Валидация файла на тип image
 * @param string $name - значение для валидации;
 */
function validateFileAndTypeImage($file)
{
    if (empty($_FILES['lot-image']['name'])) {
        return  'Вы не загрузили файл';
    }

    $filePath = $file["tmp_name"];
    $fileType = mime_content_type($filePath);

    if (!($fileType === "image/png" || $fileType === "image/jpeg")) {
        return "Изображение должно быть jpeg, jpg или png";
    }

    return null;
}
/**
 * Добавляет к тексту запятую и пробел.
 * @param string $str - текст;
 * @return string
 */
function addCommaAndSpaceText($str)
{
    $comma = ", ";

    return $comma . $str;
}
/**
 * Валидация email на регистрацию.
 * @param string $value - значение для валидации;
 * @param resource $link - соединение с БД;
 */
function validateEmailSignUp($email, $link)
{
    if (mysqli_connect_errno()) {
        return "Не удалось проверить email";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Вы ввели - не email";
    }

    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $arr = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($arr["email"] === $email) {
        return "Этот email уже зарегестрирован";
    }

    return null;
}
/**
 * Валидация email на вход
 * @param string $value - значение для валидации;
 */
function validateEmailSignIn($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Вы ввели - не email";
    }

    return null;
}
/**
 * Получение лота по его id
 * @param string $getId - передоваемый id;
 * @param resource $linkDB - соединение с бд;
 * @param array $otherData - дополнительные данные в массиве;
 */
function getLotById($getId, $linkDB, array $otherData = [])
{
    extract($otherData);
    $getId = intval($getId) < 0 ? 0 : intval($getId);
    $sqlLot = "SELECT l.id, l.date_completion, l.name, l.start_price, l.image, l.category_id, l.step, l.description, l.user_id, c.name AS category_name, b.user_id AS last_bet_user_id, IFNULL(max(b.price), l.start_price) AS price FROM lots l INNER JOIN сategories c ON l.category_id = c.id LEFT JOIN bets b ON l.id = b.lot_id WHERE l.id = $getId GROUP BY b.user_id";
    $resultLot = mysqli_query($linkDB, $sqlLot);

    if (!$resultLot) {
        $error = "Произошла ошибка в базе данных - " . mysqli_error($linkDB);

        showErrorTemplateAndDie([
        "error" => $error,
        "categories" => $categories,
        "user_name" => $user_name,
        "isAuth" => $isAuth,
        "categoriesIdCurrent" => $categoriesIdCurrent
        ]);
    }

    return mysqli_fetch_array($resultLot, MYSQLI_ASSOC);
}
/**
 * Получение ставок на лот по его id
 * @param string $getId - передоваемый id;
 * @param resource $linkDB - соединение с бд;
 * @param array $otherData - дополнительные данные в массиве;
 */
function getBetsForId($getId, $linkDB, array $otherData = [])
{
    extract($otherData);

    $sql = "SELECT b.date_create AS date, b.price, u.name FROM bets b INNER JOIN users u ON u.id = b.user_id WHERE lot_id = ? ORDER BY date_create DESC";
    $stmt = db_get_prepare_stmt($linkDB, $sql, [$getId]);
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

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
/**
 * Получение времени в формате 5 минут назад, 20 минут назад, час назад, Вчера, в 21:30, 19.03.17 в 08:21
 * @param string $time - время;
 * @param string $today - сегодняшняя дата и время;
 * @return string
 */
function getAgoText($today, $timeBet)
{
    $ago = "назад";
    $yesterday = "Вчера, в ";

    $datetime1 = date_create($timeBet);
    $datetime2 = date_create($today);
    $interval = date_diff($datetime1, $datetime2);
    // дней
    $countDay = (int) $interval->format('%a');
    // часов
    $hours = (int) $interval->format('%H');
    // минут
    $minutes = (int) $interval->format('%I');

    if ($countDay >= 1 && $countDay < 2) {
        return $yesterday . date('H:i', strtotime($timeBet));
    }

    if ($countDay === 0 && $hours >= 1) {
        return ($hours === 1 ? "" : "$hours ") . get_noun_plural_form($hours, "Час", "Часа", "Часов") . " $ago";
    }

    if ($countDay === 0 && $hours === 0) {
        return "$minutes " . get_noun_plural_form($minutes, "минута", "минуты", "минут") . " $ago";
    }

    return date('d.m.y в H:i', strtotime($timeBet));
}
/**
 * Проверяем чей лот
 * @param integer $userID - id залогиного пользователя;
 * @param integer $lotUserId - id пользователя создавшего лот;
 * @param boolean $isMyLot - признак на принадлежность лота $userID === $lotUserId;
 */
function checkWhoseLot($userID, $lotUserId)
{
    if ((int) $userID === (int) $lotUserId) {
        return "Вы не можите сделать ставку на свой лот";
    }

    return null;
}
/**
 * Проверяем чья последняя ставка на лот
 * @param integer $userID - id залогиного пользователя;
 * @param integer $lotUserId - id пользователя создавшего лот;
 * @param boolean $isMyLastBet - признак на принадлежность последней ставки $userID === $lotUserId;
 */
function checkWhoseLastBet($userID, $last_bet_user_id)
{
    if ((int) $userID === (int) $last_bet_user_id) {
        return "Вы уже сделали ставку";
    }

    return null;
}
/**
 * Проверяем закончилось ли время на лот
 * @param integer $userID - id залогиного пользователя;
 * @param integer $lotUserId - id пользователя создавшего лот;
 * @param boolean $isMyLot - признак на принадлежность лота $userID === $lotUserId;
 */
function checkDateCompletionBet($dateСompletion, $today)
{
    if (!(strtotime($dateСompletion) >= strtotime($today))) {
        return "Торг на этот лот завершен";
    }

    return null;
}
/**
 * Проверяем чья последняя ставка на лот
 * @param integer $price - цена лота;
 * @param integer $step - шаг ставки на лот;
 * @param integer $cost - ставка пользователя на лот;
 */
function checkMinBet($price, $step, $cost)
{
    $minBet = $price + $step;

    if (!($cost >= $minBet)) {
        return "Ваша ставка меньше минимальной ставки!";
    }

    return null;
}
/**
 * Получение названия категории
 * @param string $getId - передоваемый id;
 * @param resource $linkDB - соединение с бд;
 * @param array $otherData - дополнительные данные в массиве;
 */
function getCategoryName($id, $linkDB, array $otherData = [])
{
    extract($otherData);

    $sql = "SELECT name FROM сategories WHERE id = ?";
    $stmt = db_get_prepare_stmt($linkDB, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $resultCategoriesName = mysqli_stmt_get_result($stmt);

    if (!$resultCategoriesName) {
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

    return mysqli_fetch_array($resultCategoriesName, MYSQLI_ASSOC)["name"];
}
/**
 * Получение лотов без победителей, дата истечения которых меньше или равна текущей дате и поиск для таких лотов последней ставки
 * @param string $today - сегодняшняя дата с временем;
 * @param resource $linkDB - соединение с бд;
 * @param array $otherData - дополнительные данные в массиве;
 */
function getLotsWithoutWinners($today, $linkDB, array $otherData = [])
{
    extract($otherData);

    $sql = "SELECT t.lot_id, l.name AS name_lot, b.user_id AS winner_id, u.name, u.email FROM (SELECT lot_id, MAX(price) AS price FROM bets GROUP BY lot_id ) AS t INNER JOIN bets b ON t.price = b.price INNER JOIN lots l ON t.lot_id = l.id AND l.user_id_winner IS NULL AND l.date_completion <= ? INNER JOIN users u ON b.user_id = u.id";

    $stmt = db_get_prepare_stmt($linkDB, $sql, [$today]);
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

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
/**
 * Устанавливаем победителя у лота.
 * @param string $id - id лота;
 * @param resource $linkDB - соединение с бд;
 * @param array $otherData - дополнительные данные в массиве;
 */
function setWinner($lotId, $winnerId, $linkDB, array $otherData = [])
{
    extract($otherData);

    $sql = "UPDATE lots SET user_id_winner='$winnerId' WHERE id='$lotId'";
    $result = mysqli_query($linkDB, $sql);

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
}
