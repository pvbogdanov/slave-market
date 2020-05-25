build:
	docker-compose build

depinstall:
	docker-compose run --rm --user=$(id -u):$(id -g) php bash -c "composer install"

test:
	docker-compose run --rm --user=$(id -u):$(id -g) php
