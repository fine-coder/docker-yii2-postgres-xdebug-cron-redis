<?php
return [
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'database' => $_ENV['REDIS_DB']
        ]
    ]
];
