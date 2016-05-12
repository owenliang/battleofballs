<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account_op_log".
 *
 * @property integer $id
 * @property string $account_from
 * @property string $account_to
 * @property integer $date
 * @property integer $click_time
 */
class AccountOpLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_op_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['id', 'account_from', 'account_to', 'date', 'click_time'], 'required'],
            [['id', 'date', 'click_time'], 'integer'],
            [['account_from', 'account_to'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_from' => 'Account From',
            'account_to' => 'Account To',
            'date' => 'Date',
            'click_time' => 'Click Time',
        ];
    }
    
    /**
     * 获取某个用户某天的所有点击日志
     * @param unknown $accountFrom
     * @param unknown $date
     * @param unknown $accountToArr
     */
    public static function listOpLog($accountFrom, $date, $accountToArr) {
    	return self::find()->asArray(true)->where(['account_from' => $accountFrom, 'date' => $date, 'account_to' => $accountToArr])->all();
    }
    
    /**
     * 创建一条点击日志
     * @param unknown $accountFrom
     * @param unknown $accountTo
     * @param unknown $date
     * @return
     */
    public static function createOpLog($accountFrom, $accountTo, $date) {
    	$opLog = new AccountOpLog();
    	$opLog->account_from = $accountFrom;
    	$opLog->account_to = $accountTo;
    	$opLog->date = $date;
    	$opLog->ack = 0;
    	$opLog->click_time = time();
    	$ret = $opLog->save();
    	if (empty($ret)) {
    		return false;
    	}
    	return $opLog->toArray();
    } 
}
