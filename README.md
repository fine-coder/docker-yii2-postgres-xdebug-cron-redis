# Памятка Docker, Yii2, Postgres, Xdebug, Cron, Redis

* [Установка Yii2](#установка-yii2)  
* [Запуск контейнеров](#запуск-контейнеров)  
* [Инициализация проекта](#инициализация-проекта)  
* [CLI Interpreter в PhpStorm](#cli-interpreter-в-phpstorm)  
* [Env-переменные в php-файлах](#env-переменные-в-php-файлах)  
* [Подключение Postgres в Yii2](#подключение-postgres-в-yii2)  
* [Подключение Postgres в PhpStorm](#подключение-postgres-в-phpstorm)  
* [Начальная миграция](#начальная-миграция)  
* [Включение ЧПУ](#включение-чпу)  
* [Включение debug и gii](#включение-debug-и-gii)  
* [Настройка Xdebug в PhpStorm](#настройка-xdebug-в-phpstorm)  
* [Cron](#cron)  
* [pgAdmin](#pgadmin)  
* [Redis](#redis)  
* [phpRedisAdmin](#phpredisadmin)  

## Система

В файл `C:\Windows\System32\drivers\etc\hosts` добавим

```
127.0.0.1 example.loc
127.0.0.1 admin.example.loc
```

## Установка Yii2

В Windows в папке с проектами в строке пути вводим cmd, затем enter  
Вводим

```
composer create-project --prefer-dist yiisoft/yii2-app-advanced example.loc
```

example.loc - название проекта

Подробнее [здесь](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md)

## Запуск контейнеров

Запускаем Docker

Копируем в проект папку `docker` и файлы: `.env`, `docker-compose.yml`, `Makefile`

Про конфиг Apache и Nginx [здесь](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md#preparing-application)

В консоле

```
make up
```

Запуск, перезапуск, остановка контейнеров

```
make start
make restart
make stop
```

Для БД в `docker-compose.yml` прописали

```
volumes:
    - postgres_data:/var/lib/postgresql/data
```

Этот volume можно найти с именем `example_postgres_data` в приложении Docker в разделе `Volumes`

Кроме того, для unix-систем

```
  db:
    volumes:
      - ./docker/volumes/postgres:/var/lib/postgresql/data
```

Возможно для WSL 2

```
  db:
    volumes:
      - /mnt/d/projects/example.loc/docker/volumes/postgres:/var/lib/postgresql/data
```

## Инициализация проекта

В консоле

```
docker-compose run --rm php php init
```

## CLI Interpreter в PhpStorm

Идем `Settings` -> `PHP` -> `CLI Interpreter`  
Далее `From Docker, Vagrant...` -> `Docker`  
Выбираем образ PHP  
PHP Version определилась, а Debugger пишет Not installed  
Не страшно, Xdebug будет

Идем `Settings` -> `PHP` -> `PHP language level` ставим ту же версию

## Env-переменные в php-файлах

В консоле

```
docker-compose run --rm php composer require vlucas/phpdotenv
```

В `common/config/bootstrap.php` добавим

```
$dotenv = Dotenv\Dotenv::createMutable(dirname(dirname(__DIR__)));
$dotenv->load();
```

Подробнее [здесь](https://github.com/vlucas/phpdotenv)

## Подключение Postgres в Yii2

В `common/config/main-local.php` заменяем подключение к БД на

```
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => "pgsql:host={$_ENV['POSTGRES_HOST']};dbname={$_ENV['POSTGRES_DB']}",
    'username' => $_ENV['POSTGRES_USER'],
    'password' => $_ENV['POSTGRES_PASSWORD'],
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => $_ENV['POSTGRES_SCHEMA']
        ]
    ]
]
```

## Подключение Postgres в PhpStorm

`Database` -> `+` -> `Data Source` -> `PostgreSQL`
* `Host` -> `localhost`  
* `Port` -> `5432`

Из `.env` берем
* `User` -> `user`
* `Password` -> `password`
* `Database` -> `example`

Во вкладке `Schemas` -> `Schema pattern` ставим `example:public`

Для проверки соединения нажимаем `Test Connection`

## Начальная миграция

В консоле

```
docker-compose run --rm php yii migrate
```

или

```
make migrate
```

## Включение ЧПУ

Раскомментируем `urlManager` в `frontend/config/main.php` и `backend/config/main.php`

В `frontend/config/main.php`

```
'rules' => [
    '<alias:\w+>' => 'site/<alias>'
]
```

Подробнее [здесь](https://github.com/samdark/yii2-cookbook/blob/master/book/enable-pretty-urls.md)

## Включение debug и gii

В файле `backend/config/main-local.php` добавим `'allowedIPs' => ['*']` для debug и gii

```
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs' => ['*']
];

$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['*']
];
```

В файле `frontend/config/main-local.php` добавим `'allowedIPs' => ['*']` для debug

## Настройка Xdebug в PhpStorm

В консоле

```
docker-compose run --rm php bash
```

Затем

```
php -i
```

Ищем в блоке Xdebug

```
Debugger => enabled
IDE Key => PHPStorm
```

Порт

```
xdebug.client_port => 9005
```

Установим расширение `Xdebug helper` для браузера  
В настройках `IDE Key` поставим `PHPStorm`

В PhpStorm `Settings` -> `PHP` -> `Debug` -> `Xdebug` -> `Debug port` ставим `9005`  
Затем `Settings` -> `PHP` -> `Servers` -> `+`  
* `Name` -> любое, хотя говорят в `docker-compose.yml` -> `php` -> `environment` нужно задать `PHP_IDE_CONFIG=serverName=Example` и использовать его  
* `Host` -> `localhost`  
* `Port` -> `80`  
* `Debugger` -> `Xdebug`  
* `Use path mappings` -> ставим галочку  
* Ниже для папки проекта ставим `Absolute path on the server` -> `/app`, берем из `docker-compose.yml` -> `php` -> `volumes`

В PhpStorm справа сверху `Run/Debug Configurations` -> `+` -> `PHP Remote Debug`  
* `Name` -> `Xdebug`  
* `Filter debug connection by IDE key` -> ставим галочку  
* `Server` -> выбираем созданный  
* `IDE key` -> `PHPStorm`

#### Отладка
* В браузере `Xdebug helper` ставим в `Debug` (зеленый жук)  
* В PhpStorm задаём в коде брейкпоинт (точку останова), активируем Xdebug (зеленый жук справа сверху)  
* Перезагружаем страницу в браузере

В отладчике должны отобразиться переменные и их значения на момент обработки кода в брейкпоинте  
При нажатии F8 выполняется код на текущей строке и останавливается перед следующим действием  
При нажатии F9 программа продолжит выполнение до следующего брэйкпоинта, если их больше не встретится, программа просто завершит свою работу

## Cron

Сервис cron в `docker-compose.yml`

Добавлены файлы
* docker/cron/Dockerfile
* docker/cron/crontab
* cron-test.php

Отключение cron

```
docker rm -f example-cron
```

Это остановит и удалит контейнер

Пригодится [ссылка](https://stackoverflow.com/questions/37458287/how-to-run-a-cron-job-inside-a-docker-container#answer-37458519)

## pgAdmin

В `docker-compose.yml` добавляем

```
pgadmin:
  container_name: ${COMPOSE_PROJECT_NAME}-pgadmin
  image: dpage/pgadmin4
  environment:
    PGADMIN_DEFAULT_EMAIL: admin@example.loc
    PGADMIN_DEFAULT_PASSWORD: password
  ports:
    - "5050:80"
```

В консоле

```
make up
```

pgAdmin доступен по адресу http://example.loc:5050

Нажимаем `Добавить новый сервер`  
На вкладке `General` задаем `Имя`  
На вкладке `Соединение` задаем
* `Имя/адрес сервера` -> `db` (название сервиса из `docker-compose.yml`)
* `Имя пользователя` -> `user` (из файла `.env`)
* `Пароль` -> `password` (из файла `.env`)

## Redis

В Yii2 должен быть `yiisoft/yii2-redis`

В конфигурацию приложения добавим

```
'components' => [
    ...
    'redis' => [
        'class' => 'yii\redis\Connection',
        'hostname' => 'localhost',
        'port' => 6379,
        'database' => 0
    ],
    ...
]
```

Для проверки

```
Yii::$app->redis->set('mykey', 'some value');
echo Yii::$app->redis->get('mykey');
```

Взято [здесь](https://www.yiiframework.com/extension/yiisoft/yii2-redis/doc/guide/2.0/ru/installation)

В cli заходим так

```
docker exec -it example-redis redis-cli
```

Возможное использование Redis: очереди, кэш, сессии  
Подробнее [здесь](https://github.com/yiisoft/yii2-redis/tree/master/docs/guide-ru)  
Список команд [здесь](https://redis.io/commands/)

## phpRedisAdmin

Параметры
* REDIS_1_HOST - define host of the Redis server
* REDIS_1_NAME - define name of the Redis server
* REDIS_1_PORT - define port of the Redis server
* REDIS_1_AUTH - define password of the Redis server
* ADMIN_USER - define username for user-facing Basic Auth
* ADMIN_PASS - define password for user-facing Basic Auth

