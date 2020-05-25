select max(price), min(price), avg(price)
from slave where weight > 60;

select *
from (
         select count(sc.category_id) as count
         from slave
                  inner join slave_category sc on slave.id = sc.slave_id
     ) t
where t.count > 10

select max(sum) from (
                         select sum(price) as sum
                         from slave
                                  inner join slave_category sc on slave.id = sc.slave_id
                         group by sc.category_id
                     ) t

WITH men AS (
    select count(slave_id) as count, sc.category_id
    from slave
             inner join slave_category sc on slave.id = sc.slave_id and sex = true
    group by sc.category_id
),
     women AS (
         select count(slave_id) as count, sc.category_id
         from slave
                  inner join slave_category sc on slave.id = sc.slave_id and sex = false
         group by sc.category_id
     )
SELECT *
FROM men
         inner join women on women.category_id = men.category_id
    and men.count > women.count

select *
from category
where path <@ (
    select id
    from category
    where name = 'Кухня'
)::text::ltree

create extension ltree;

create table category
(
    id   serial  not null
        constraint category_pk
            primary key,
    name varchar not null,
    path ltree
);

create table slave
(
    id     serial  not null
        constraint slave_pk
            primary key,
    weight integer not null,
    price  integer not null,
    sex    boolean not null
);

create table slave_category
(
    slave_id    integer not null
        constraint slave_category_slave_id_fk
            references slave
            on update cascade on delete cascade,
    category_id integer not null
        constraint slave_category_category_id_fk
            references category
            on update cascade on delete cascade,
    constraint slave_category_pk
        primary key (slave_id, category_id)
);

INSERT INTO public.slave (id, weight, price, sex) VALUES (2, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (4, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (5, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (7, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (8, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (9, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (10, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (12, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (13, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (14, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (15, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (16, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (17, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (18, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (19, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (20, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (21, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (22, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (23, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (24, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (25, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (26, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (27, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (28, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (29, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (30, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (31, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (32, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (33, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (34, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (35, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (36, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (37, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (38, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (39, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (6, 70, 33, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (11, 70, 100, true);
INSERT INTO public.slave (id, weight, price, sex) VALUES (1, 70, 33, false);
INSERT INTO public.slave (id, weight, price, sex) VALUES (3, 70, 33, false);

INSERT INTO public.category (id, name, path) VALUES (1, 'Кухня', 1);
INSERT INTO public.category (id, name, path) VALUES (2, 'Полевая кухня', 1.2);
INSERT INTO public.category (id, name, path) VALUES (3, 'Подполевая кухня', 1.2.3);
INSERT INTO public.category (id, name, path) VALUES (4, 'рынок', 4);
INSERT INTO public.category (id, name, path) VALUES (5, 'поставки', 4.5);

INSERT INTO public.slave_category (slave_id, category_id) VALUES (1, 1);
INSERT INTO public.slave_category (slave_id, category_id) VALUES (2, 1);
INSERT INTO public.slave_category (slave_id, category_id) VALUES (2, 2);
INSERT INTO public.slave_category (slave_id, category_id) VALUES (3, 1);
