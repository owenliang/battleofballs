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
            [['id', 'account_from', 'account_to', 'date', 'click_time'], 'required'],
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
}
