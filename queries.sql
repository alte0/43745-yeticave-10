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
INSERT INTO users (name, email, password, image, contacts)
  VALUES 
    ('Максим', 'makc@ya.ru', '@123Rth', '/images/pic.jpg', '+79998887766'),
    ('Василий', 'vasya@ya.ru', '@546jhg', '/images/pic2.jpg', '+75554443322');
-- Добавляем список объявлений.
INSERT INTO lots (name, description, start_price, image, step, date_completion, user_id, category_id)
  VALUES 
    ('2014 Rossignol District Snowboard', 'Отличное состояние, как новый!', 10999, '/imgs/lot-1.jpg', 100, '2019-08-25 12:25', 1, 1),
    ('DC Ply Mens 2016/2017 Snowboard', 'Немного краска слезла', 159999, '/imgs/lot-2.jpg', 1000, '2019-08-26 10:15', 1, 1),
    ('Крепления Union Contact Pro 2015 года размер L/XL', 'Не разу не пользовался!', 8000, '/imgs/lot-3.jpg', 100, '2019-08-26 20:00', 1, 2),
    ('Ботинки для сноуборда DC Mutiny Charocal', 'Эксклюзивные!', 10999, '/imgs/lot-4.jpg', 100, '2019-08-26 18:00', 1, 3),
    ('Куртка для сноуборда DC Mutiny Charocal', 'Пару сезонов откатал!', 7500, '/imgs/lot-5.jpg', 100, '2019-08-26 15:00', 1, 4),
    ('Маска Oakley Canopy', 'Отдаю почти даром!', 5400, '/imgs/lot-6.jpg', 50, '2019-08-26 13:00', 1, 6);
-- Добавляем пару ставок для любого объявления.
INSERT INTO bets (price, user_id, lot_id)
  VALUES 
    (11999, 2, 1),
    (12999, 2, 1),
    (8100, 2, 3),
    (9100, 2, 3);
-- Получить все категории.
SELECT * FROM сategories;
-- Получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории.
SELECT l.id, l.date_create, l.date_completion, l.name, l.start_price, l.image, l.category_id, c.name, b.price FROM lots l
INNER JOIN сategories c ON l.category_id = c.id
LEFT JOIN bets b ON l.id = b.lot_id
WHERE l.date_completion >= "2019-08-26 13:15";
-- Показать лот по его id. Получите также название категории, к которой принадлежит лот.
SELECT l.*, c.name FROM lots l
INNER JOIN сategories c ON l.category_id = c.id
WHERE l.id = 1;
-- Обновить название лота по его идентификатору.
UPDATE lots SET name='новое название лота - Маска Oakley Canopy' WHERE id=6;
-- Получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM bets WHERE lot_id = 3 ORDER BY date_create DESC;