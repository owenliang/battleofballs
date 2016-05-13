<?php

namespace frontend\controllers;

use common\lib\helpers\HttpResponse;
use common\lib\consts\ErrorCode;
use common\models\AccountBasicInfo;

class AccountController extends \yii\web\Controller
{
	/**
	 * 无密码登陆账号
	 * 
	 * 由于无密码登陆，不存服务端会话，直接保存浏览器cookie记录登陆账号即可
	 * @param
	 * @return
	 */
    public function actionLogin() {
    	$username = \Yii::$app->request->post('username');
    	$shareUrl = \Yii::$app->request->post('shareUrl');
    	if (empty($username) || empty($shareUrl)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_PARAM_ERROR, '', []);
    	}

    	$account = AccountBasicInfo::findAccount($username);
    	if (empty($account)) { // 不存在，则创建账号
    		$accountInfo = [
    				'shareUrl' => $shareUrl, 
    		];
    		$succeed = AccountBasicInfo::createAccount($username, $accountInfo);
    		if (empty($succeed)) {
    			return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '初始化账号失败', []);
    		}
    	} else { // 已存在，更新分享链接 和 最近登陆时间
    		AccountBasicInfo::updateAll(['last_login' => time(), 'share_url' => $shareUrl], ['username' => $username]);
    	}

    	\Yii::$app->response->cookies->add(new \yii\web\Cookie([
    			'name' => 'battleofballs_username', 
    			'value' => $username, 
    			'expire' => time() + \Yii::$app->params['Account']['sessionExpire'], 
    	]));
    	
    	return HttpResponse::packReturn(0, 'success', [
    		'username' => $username, 
    		'shareUrl' => $shareUrl,
    	]);
    }

    /**
     * 获取登陆状态
     * 
     * 直接校验cookie中的账号信息即可，无服务端会话维持
     * @param
     * @return
     */
    public function actionGetStatus() {
    	$username = \Yii::$app->request->cookies->get('battleofballs_username');
    	if (empty($username)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_USER_NOT_LOGIN, '用户未登录', []);
    	}
    	$account = AccountBasicInfo::findAccount($username);
    	if (empty($account)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '账号不存在', []);
    	}
    	return HttpResponse::packReturn(0, 'success', [
    			'username' => $account['username'], 
    			'shareUrl' => $account['share_url'], 
    			'historyScore' => $account['history_score'],
    			'historyClick' => $account['history_click'], 
    			'lastLogin' => $account['last_login'],  
    	]);
    }
}
