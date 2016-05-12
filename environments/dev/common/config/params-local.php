<?php
return [
		'Account' => [
			'sessionExpire' =>  86400 * 90, // 90天COOKIE过期
		], 
		'Task' => [
			'fullScore' => 5, // 每天最多领取5个棒棒糖
			'taskSize' => 100, // 一次最多获取100个任务
		]
];
