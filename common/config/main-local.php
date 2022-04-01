<?php

return [
    'components' => [
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
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
