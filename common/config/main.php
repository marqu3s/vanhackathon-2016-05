<?php
return [
    'id' => 'vanhackathon',
    'name' => 'Vanhackathon',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    //'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        YII_ENV_DEV ? 'jquery.min.js' : '//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js',
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        '/css/flatly.min.css'
                    ]
                ]
            ],
        ],
        'cache' => [
            'class' => YII_ENV_DEV ? 'yii\caching\DummyCache' : 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=172.17.0.2;dbname=vanhackathon-2016-05-joao-marques',
            //'dsn' => 'mysql:host=10.70.20.188;dbname=vanhackathon-2016-05-joao-marques',
            'username' => 'vanhack',
            'password' => 'vanhack2016',
            'charset' => 'utf8',
        ],
        /*'mailer' => [
            'class' => 'shershennm\sendgrid\Mailer',
            'username' => '',
            'password' => '',
            'viewPath' => '@common/mail',
        ],*/
    ],
    'modules' => [
        /*'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module'
        ]*/
    ],
];
