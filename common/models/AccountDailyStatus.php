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
            [['id', 'username', 'date', 'score', 'last_score_time', 'click_count', 'last_click_time'], 'required'],
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
}
