<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ### LOGIN ###
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'login',
                    'pluralize' => false,
                    'patterns' => [
                        'POST,GET' => 'index',
                    ]
                ],

                ### GAMES ###
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/game'],
                    'pluralize' => false,
                    //'only' => ['index', 'create']
                    'patterns' => [
                        'GET' => 'index',
                        'POST new' => 'new',
                        'POST join' => 'join',
                        'POST start' => 'start',
                        'POST leave' => 'leave',
                        'POST guess' => 'guess',
                    ]
                ],

                ### USERS/PLAYERS ###
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/player']
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'api\modules\v1\models\Player',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null
        ],
    ],

    // Using modules for versioning the API
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
