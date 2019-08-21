<?php
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
function is_date_valid(string $date) : bool {
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
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
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
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
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
function include_template($name, array $data = []) {
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
 * @param number $num Форматируемая сумма
 * @return string отформатированная сумма вместе со знаком рубля
 */
function formatPrice (float $num):string {
    $num = ceil($num);
    if ($num >= 1000) {
        $num = number_format($num, 0, '.', ' ');
    }

    return $num . '<b class="rub">р</b>';
}

/**
 * Функция очистки данных от тэгов
 * @param string $num Очишаемая строка
 * @return string Очишенная строка
 */
function clearStrDataTags($str) {
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция добаления 0-ей до 2-х знаков
 * @param string $str 
 * @return string 
 */
function addStrPadZero($str) {
    return str_pad($str, 2, "0", STR_PAD_LEFT);
}
/**
 * Функция вычисления оставшегося времени в формате «ЧЧ:ММ»
 * @param date $date дата в формате ГГГГ-ММ-ДД;
 * @return array [09, 29] - «ЧЧ:ММ»
 */
function calcDateExpiration($date): array {
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
 * Функция показывает шаблон ошибок 
 * @param error $error - текст ошибки;
 */
function showErrorTemplate(array $data = []) {
    extract($data);
    $content = include_template('error.php', ['error' => $error]);
    $layout = include_template(
        'layout.php',
        [
            "title" => "Ошибка - YetiCave",
            "categories" => $categories, 
            "content" => $content,
            "user_name" => $user_name,
            "is_auth" => $is_auth
        ]
    );
    print($layout);
 }