<?php

namespace frontend\controllers;

use common\models\AccountBasicInfo;
use common\models\AccountDailyStatus;
use common\lib\helpers\HttpResponse;
use common\lib\consts\ErrorCode;
use common\models\AccountOpLog;

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

    /**
     * 获取任务
     * @param
     * @return
     */
    public function actionListTask() {
    	$account = $this->checkLogin();
    	if (empty($account)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_USER_NOT_LOGIN, '用户未登录', []);
    	}
    	
    	$date = \Yii::$app->request->get('date');
    	if (empty($date)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_PARAM_ERROR, '参数缺失', []);
    	}
    	
    	// 获取其他用户的点击信息
    	$dateTimestamp = strtotime($date);
    	$statusArr = AccountDailyStatus::listDailyStatus($account['username'], $dateTimestamp, 
    			\Yii::$app->params['Task']['fullScore'], \Yii::$app->params['Task']['taskSize']);
    	
    	// 将自己此前已经点击过的用户找出来
    	$accountToArr = [];
    	foreach ($statusArr as $status) {
    		$accountToArr[] = $status['username'];
    	}
    	
    	// 点击过的用户应该置灰
    	$opLog = AccountOpLog::listOpLog($account['username'], $dateTimestamp, $accountToArr);
    	$opMap = [];
    	foreach ($opLog as $log) {
    		if (!empty($log['ack'])) { // 只有真正生效的点击，才置灰
    			$opMap[$log['account_to']] = 1;
    		}
    	}
    	
    	$ret = [];
    	foreach ($statusArr as $status) {
    		$ret['statusArr'][] = [
    				'username' => $status['username'], 
    				'date' => $date, 
    				'score' => $status['score'], 
    				'lastScoreTime' => $status['last_score_time'], 
    				'clickCount' => $status['click_count'],
    				'lastClickTime' => $status['last_click_time'],
    				'enabled' => isset($opMap[$status['username']]) ? false : true
    		];
    	}
    	return HttpResponse::packReturn(0, 'success', $ret);
    }
    
    /**
     * 点击某个任务
     * @param
     * @return
     */
    public function actionClickTask() {
    	$account = $this->checkLogin();
    	if (empty($account)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_USER_NOT_LOGIN, '用户未登录', []);
    	}
    	 
    	$username = \Yii::$app->request->post('username');
    	$date = \Yii::$app->request->post('date');
    	if (empty($username) || empty($date)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_PARAM_ERROR, '参数缺失', []);
    	}
    	
    	$dateTimestamp = strtotime($date);
    	$today = strtotime(date('Y-m-d', time()));
    	if ($dateTimestamp != $today) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '只能点击今天的任务', []);
    	}
    	
    	$accountTo = AccountBasicInfo::findAccount($username);
    	if (empty($accountTo)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '用户不存在', []);
    	}
    	
    	// 查询是否已存在这条点击记录, 存在返回成功
    	$opLog = AccountOpLog::listOpLog($account['username'], $dateTimestamp, [$username]);
    	if (empty($opLog)) {
    		$opLog = AccountOpLog::createOpLog($account['username'], $username, $dateTimestamp);
    		if (empty($opLog)) {
    			return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '点击日志生成失败', []);
    		}
    	}
    	return HttpResponse::packReturn(0, 'success', [
    			'username' => $username, 
    			'date' => $date, 
    			'shareUrl' => $accountTo['share_url'], 
    	]);
    }
    
    /**
     * 确认任务完成
     * @param
     * @return
     */
    public function actionAckTask() {
    	$account = $this->checkLogin();
    	if (empty($account)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_USER_NOT_LOGIN, '用户未登录', []);
    	}
    	
    	$username = \Yii::$app->request->get('username');
    	$date = \Yii::$app->request->get('date');
    	if (empty($username) || empty($date)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_PARAM_ERROR, '参数缺失', []);
    	}
    	
    	$dateTimestamp = strtotime($date);
    	$today = strtotime(date('Y-m-d', time()));
    	if ($dateTimestamp != $today) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '只能点击今天的任务', []);
    	}
    	
    	// 点击记录必须存在
    	$opLog = AccountOpLog::listOpLog($account['username'], $dateTimestamp, [$username]);
    	if (empty($opLog)) {
    		return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '点击记录未生成', []);
    	}
    	$opLog = $opLog[0];
    	
    	// 该点击未确认，那么首先确认，然后修改积分
    	if (empty($opLog['ack'])) {
    		// 带条件更新ack，更新失败则不能累加积分，返回成功即可
    		$success = AccountOpLog::updateAll(['ack' => 1], ['account_from' => $account['username'], 'account_to' => $username, 'ack' => 0]);
    		if (!empty($success)) { // ack成功，开始计算积分
    			// 给点击者增加点击次数
    			$fromStatus = AccountDailyStatus::findDailyStatus($account['username'], $dateTimestamp);
    			if (empty($fromStatus)) {
    				$fromStatus = AccountDailyStatus::createDailyStatus($account['username'], $dateTimestamp);
    				if (empty($fromStatus)) {
    					return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '无法获取点击者当日信息', []);
    				}
    			}
    			\Yii::$app->db->createCommand("update account_daily_status set `click_count`=`click_count`+1, last_click_time=:time " .  
    					"where username=:username and date=:date", [
    					':time' => time(), 'username' => $account['username'], 'date' => $dateTimestamp
    			])->execute();
    					
    			// 给被点击者增加积分
    			$toStatus = AccountDailyStatus::findDailyStatus($username, $dateTimestamp);
    			if (empty($toStatus)) {
    				$toStatus = AccountDailyStatus::createDailyStatus($username, $dateTimestamp);
    				if (empty($toStatus)) {
    					return HttpResponse::packReturn(ErrorCode::ERR_COMMON_SYSTEM_ERROR, '无法获取被点击者当日信息', []);
    				}
    			}
    			\Yii::$app->db->createCommand("update account_daily_status set `score`=`score`+1, last_score_time=:time " .
    					"where username=:username and date=:date", [
    					':time' => time(), 'username' => $username, 'date' => $dateTimestamp
    			])->execute();
    		}
    	}
    	return HttpResponse::packReturn(0, 'success', [
    			'username' => $username, 
    			'date' => $date, 
    	]);
    }
}
