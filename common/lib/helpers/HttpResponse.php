<?php

namespace common\lib\helpers;

use yii\web\Response;

final class HttpResponse {

	/**
	 * 封装对外http接口的返回结果，格式为json
	 * @param int $errno
	 * @param string $errMsg
	 * @param array $data
	 */
	public static function packReturn($errno = 0, $errMsg = 'success', $data = []) {
		$response = \Yii::$app->response;
		$response->format = \yii\web\Response::FORMAT_JSON;
		$response->data = [
			'errno' => $errno,
			'msg' => $errMsg,
			'data' => $data,
		];
		header("Access-Control-Allow-Origin: *");
	}
}