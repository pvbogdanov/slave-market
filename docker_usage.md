#### Собрать образы:

```
docker-compose build 
```

#### Установить composer-зависимости:

```
docker-compose run --rm --user=$(id -u):$(id -g) php bash -c "composer install"
```

#### Запустить тесты:

```
docker-compose run --rm --user=$(id -u):$(id -g) php
```

или

```
docker-compose run --rm --user=$(id -u):$(id -g) php bash -c "./vendor/bin/phpunit tests"
```