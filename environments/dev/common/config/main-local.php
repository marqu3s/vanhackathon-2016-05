<?php
return [
    'components' => [
        'assetManager' => [
            'linkAssets' => false,
        ],
        'db' => [
            'dsn' => 'mysql:host=172.17.0.2;dbname=vanhackathon-2016-05',
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
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'] // adjust this to your needs
        ],
    ],
];
