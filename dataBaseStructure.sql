CREATE TABLE slaves (
    id PRIMARY KEY AUTOINCREMENT NOT NULL,
    name varchar(255),
    sex  char(1) NOT NULL DEFAULT 'm',
    age  int,
    weight int,
    skin_color varchar(255),
    born varchar(255),
    comment text,
    price int NOT NULL DEFAULT 0
);

CREATE TABLE categories (
    id PRIMARY KEY AUTOINCREMENT NOT NULL,
    title varchar(255),
    parent_id int NOT NULL DEFAULT 0
);

CREATE TABLE categories_and_slaves_relationship (
    id PRIMARY KEY AUTOINCREMENT NOT NULL,
    slave_id int NOT NULL,
    category_id int NOT NULL
);


/* Получить минимальную, максимальную и среднюю стоимость всех рабов весом более 60 кг. */
SELECT MIN(`price`), MAX(`price`),  AVG(`price`) FROM `slaves` WHERE `weight` > 60;

/* Выбрать категории, в которых больше 10 рабов. */
SELECT `c`.`title` AS `title`, `c`.`id` AS `id`
    FROM `categories_and_slaves_relationship` AS `relation`
    INNER JOIN `categories` AS `c` ON `relation`.`category_id` = `c`.`id`
    WHERE COUNT(`relation`.slave_id) > 10;

/* Выбрать категорию с наибольшей суммарной стоимостью рабов. */
SELECT `c`.`title` AS `title`, `c`.`id` AS `id`
    FROM `categories_and_slaves_relationship` AS `relation`
    INNER JOIN `slaves` ON `relation`.`slave_id` = `slaves`.`id`
    WHERE SUM(`slaves`.`price`) = MAX(SUM(`slaves`.`price`));

/* Выбрать категории, в которых мужчин больше, чем женщин. */
SELECT `c`.`title` AS `title`, `c`.`id` AS `id`
FROM `categories_and_slaves_relationship` AS `relation`
    INNER JOIN `slaves` ON `relation`.`slave_id` = `slaves`.`id`
    WHERE SUM(`slaves`.`sex` = 'm') > SUM(`slaves`.`sex` = 'f');

/* Количество рабов в категории "Для кухни" (включая все вложенные категории). */
SELECT COUNT(`relation`.`slave_id`), `c`.`id` AS `id`
    FROM `categories_and_slaves_relationship` AS `relation`
    INNER JOIN `categories` AS `c` ON `relation`.`category_id` = `c`.`id`
    WHERE `c`.`title` = 'Для кухни' OR `c`.`parent_id` = `id`;