<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account_daily_status".
 *
 * @property integer $id
 * @property string $username
 * @property integer $date
 * @property integer $score
 * @property integer $last_score_time
 * @property integer $click_count
 * @property integer $last_click_time
 */
class AccountDailyStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_daily_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['id', 'username', 'date', 'score', 'last_score_time', 'click_count', 'last_click_time'], 'required'],
            [['id', 'date', 'score', 'last_score_time', 'click_count', 'last_click_time'], 'integer'],
            [['username'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'date' => 'Date',
            'score' => 'Score',
            'last_score_time' => 'Last Score Time',
            'click_count' => 'Click Count',
            'last_click_time' => 'Last Click Time',
        ];
    }
    
    /**
     * 查询用户某一天的积分信息
     * @param unknown $username
     * @param unknown $date
     * @return
     */
    public static function findDailyStatus($username, $date) {
    	return self::find()->asArray(true)->where(['username' => $username, 'date' => $date])->one();
    }
    
    /**
     * 创建用户某一天的积分信息
     * @param unknown $username
     * @param unknown $date
     * @return
     */
    public static function createDailyStatus($username, $date) {
    	$dailyStatus = new AccountDailyStatus();
    	$dailyStatus->username = $username;
    	$dailyStatus->date = $date;
    	$dailyStatus->score = 0;
    	$dailyStatus->last_score_time = 0;
    	$dailyStatus->click_count = 0;
    	$dailyStatus->last_click_time = 0;
    	$ret = $dailyStatus->save();
    	if (empty($ret)) {
    		return false;
    	}
    	return $dailyStatus->toArray();
    }
    
    /**
     * 查询某天尚需点击的其他用户列表
     * @param unknown $username
     * @param unknown $date
     * @param unknown $scoreLimit
     * @param unknown $countLimit
     * @return
     */
    public static function listDailyStatus($username, $date, $scoreLimit, $countLimit) {
    	return self::find()->asArray(true)->where(['date' => $date])->andWhere(['<', 'score', $scoreLimit])->
    		andWhere(['!=', 'username', $username])->orderBy('click_count desc, last_click_time desc')->limit($countLimit)->all();
    }
}
