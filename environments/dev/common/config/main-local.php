<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=battleofballs',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SendmailTransport',
			]
		],
    	'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
    	],
    ],
];
