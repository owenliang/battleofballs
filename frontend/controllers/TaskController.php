<?php

namespace frontend\controllers;

use common\models\AccountBasicInfo;
use common\models\AccountDailyStatus;
use common\lib\helpers\HttpResponse;
use common\lib\consts\ErrorCode;

class TaskController extends \yii\web\Controller
{
	/**
	 * 校验用户登陆状态
	 */
	private function checkLogin() {
		$username = \Yii::$app->request->cookies->get('battleofballs_username');
		if (empty($username)) {
			return false;
		}
		$account = AccountBasicInfo::findAccount($username);
		if (empty($account)) {
			return false;
		}
		return $account;
	}
	
	/**
	 * 刷新用户的最新状态
	 * @param
	 * @return
	 */
    public function actionFreshStatus()
    {
        $account = $this->checkLogin();
        if (empty($account)) {
        	return HttpResponse::packReturn(ErrorCode::ERR_COMMON_USER_NOT_LOGIN, '用户未登录', []);
        }
        
        $date = \Yii::$app->request->get('date');
        if (empty($date)) {
        	return HttpResponse::packReturn(ErrorCode::ERR_COMMON_PARAM_ERROR, '参数缺失', []);
        }
        
        $dateTimestamp = strtotime($date);
        $dailyStatus = AccountDailyStatus::findDailyStatus($account['username'], $dateTimestamp);
        if (empty($dailyStatus)) { // 还没生成当天的记录
        	$dailyStatus = AccountDailyStatus::createDailyStatus($account['username'], $dateTimestamp);
        	if (empty($dailyStatus)) {
        		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '初始化积分信息失败', []);
        	}
        }
        return HttpResponse::packReturn(0, 'success', [
        		'username' => $dailyStatus['username'], 
        		'date' => $date, 
        		'score' => $dailyStatus['score'], 
        		'lastScoreTime' => $dailyStatus['last_score_time'],
        		'clickCount' => $dailyStatus['click_count'],
        		'lastClickTime' => $dailyStatus['last_click_time'], 
        		'historyScore' => $account['history_score'], 
        		'historyClick' => $account['history_click'],
        ]);
    }

    
}
