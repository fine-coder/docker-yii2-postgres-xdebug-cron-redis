up: build_php composer_cr
	docker-compose up -d --build --remove-orphans
down:
	docker-compose down
build_php:
	docker-compose build php
composer_cr:
	docker-compose run --rm php composer cr
start:
	docker-compose start
restart:
	docker-compose restart
stop:
	docker-compose stop
migrate:
	docker-compose run --rm php yii migrate
shell-php:
	docker-compose run --rm php bash
shell-db:
	docker-compose run --rm db bash