USE `yeticave`;
-- Добавляем список категорий;
INSERT INTO сategories (name, character_code)
  VALUES 
    ('Доски и лыжи', 'boards'),
    ('Крепления', 'attachment'),
    ('Ботинки', 'boots'),
    ('Одежда', 'clothing'),
    ('Инструменты', 'tools'),
    ('Разное', 'other');
-- Добавляем пользователей;
INSERT INTO users (name, email, password, contacts)
  VALUES 
    ('Максим', 'makc@test.test', '@123Rth', '+79998887766'),
    ('Николай', 'kolya@test.test', '@173Rth', '+79998887766'),
    ('Василий', 'vasya@test.test', '@546jhg', '+75554443322');
-- Добавляем список объявлений.
INSERT INTO lots (name, description, start_price, image, step, date_completion, user_id, category_id)
  VALUES 
    ('2014 Rossignol District Snowboard', 'Отличное состояние, как новый!', 10999, 'img/lot-1.jpg', 100, '2019-10-04 00:00', 1, 1),
    ('DC Ply Mens 2016/2017 Snowboard', 'Немного краска слезла', 159999, 'img/lot-2.jpg', 1000, '2019-10-8 00:00', 1, 1),
    ('Крепления Union Contact Pro 2015 года размер L/XL', 'Не разу не пользовался!', 8000, 'img/lot-3.jpg', 100, '2019-10-02 00:00', 1, 2),
    ('Ботинки для сноуборда DC Mutiny Charocal', 'Эксклюзивные!', 10999, 'img/lot-4.jpg', 100, '2019-10-03 00:00', 1, 3),
    ('Куртка для сноуборда DC Mutiny Charocal', 'Пару сезонов откатал!', 7500, '/img/lot-5.jpg', 100, '2019-10-01 00:00', 1, 4),
    ('Маска Oakley Canopy', 'Отдаю почти даром!', 5400, 'img/lot-6.jpg', 50, '2019-10-10 00:00', 1, 6);
-- Добавляем пару ставок для любого объявления.
INSERT INTO bets (price, user_id, lot_id)
  VALUES 
    (11999, 2, 1),
    (12999, 3, 1),
    (8100, 2, 3),
    (9100, 3, 3);
-- Получить все категории.
SELECT * FROM сategories;
-- Получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории.
SELECT l.id, l.date_create, l.date_completion, l.name, l.start_price, l.image, l.category_id, c.name AS category_name, IFNULL(max(b.price), l.start_price) AS price FROM lots l
INNER JOIN сategories c ON l.category_id = c.id
LEFT JOIN bets b ON l.id = b.lot_id
WHERE l.date_completion >= "2019-08-26 13:15"
GROUP BY id
;
-- Показать лот по его id. Получите также название категории, к которой принадлежит лот.
SELECT l.*, c.name FROM lots l
INNER JOIN сategories c ON l.category_id = c.id
WHERE l.id = 1;
-- Обновить название лота по его идентификатору.
UPDATE lots SET name='новое название лота - Маска Oakley Canopy' WHERE id=6;
-- Получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM bets WHERE lot_id = 3 ORDER BY date_create DESC;