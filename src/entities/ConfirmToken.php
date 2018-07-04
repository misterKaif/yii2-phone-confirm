<?php

namespace teimur8\yiiPhoneConfirm\entities;

use common\entities\queries\ConfirmTokenQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%confirm_token}}".
 *
 * @property integer $id
 * @property integer $attempt_no
 * @property integer $type
 * @property integer $action
 * @property integer $token
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class ConfirmToken extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%confirm_token}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'action', 'token', 'user_id'], 'required'],
            [['attempt_no', 'type', 'action', 'token', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'attempt_no' => Yii::t('app', 'Attempt No'),
            'type' => Yii::t('app', 'Type'),
            'action' => Yii::t('app', 'Action'),
            'token' => Yii::t('app', 'Token'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ConfirmTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\entities\queries\ConfirmTokenQuery(get_called_class());
    }
}
