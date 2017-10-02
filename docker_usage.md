## Docker

+ [Документация docker](https://www.docker.com/)
+ [docker-compose](https://docs.docker.com/compose/)


#### Собрать образы:

```
cd /path/to/slave-market
docker-compose build 
```

#### Установить composer-зависимости:

```
cd /path/to/slave-market
docker-compose run --rm --user=$(id -u):$(id -g) php bash -c "composer install"
```

`--user=$(id -u):$(id -g)` - запуск команды от имени вашего пользователя. Composer не рекомендуется запускать с правами **root**.

#### Запустить тесты:

```
cd /path/to/slave-market
docker-compose run --rm --user=$(id -u):$(id -g) php
```

или

```
cd /path/to/slave-market
docker-compose run --rm --user=$(id -u):$(id -g) php bash -c "./vendor/bin/phpunit tests"
```