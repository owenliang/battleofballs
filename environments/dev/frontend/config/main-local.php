<?php

$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
        ],
    	'log' => [
    		'traceLevel' => YII_DEBUG ? 3 : 0,
    		'targets' => [
    			[
    				'class' => 'yii\log\FileTarget',
    				'levels' => ['info', 'trace', 'warning', 'error'],
    				'logFile' => '@app/../logs/frontend/battleofballs.log',
    				// 'logVars' => [],
    				// 'categories' => [],
    			],
    		],
    	]
    ]
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
