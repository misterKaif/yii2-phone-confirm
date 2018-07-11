<?php

namespace Teimur\YiiPhoneConfirm\entities;

use Teimur\YiiPhoneConfirm\Config;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenAction;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenType;
use Yii;
use yii\behaviors\TimestampBehavior;
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
 * @property integer $try_count
 * @property string $created_at
 * @property string $expires_at
 * @property string $updated_at
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
    
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'action', 'token', 'user_id'], 'required'],
            [['attempt_no', 'type', 'action', 'token', 'user_id','expires_at','try_count' ], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Config::getUserClass(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'attempt_no' => Yii::t('app', 'Attempt No'),
            'type'       => Yii::t('app', 'Type'),
            'action'     => Yii::t('app', 'Action'),
            'token'      => Yii::t('app', 'Token'),
            'user_id'    => Yii::t('app', 'User ID'),
            'expires_at' => Yii::t('app', 'Expires At'),
            'try_count'  => Yii::t('app', 'Try Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Config::getUserClass(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ConfirmTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConfirmTokenQuery(get_called_class());
    }
    
    public function wait($seconds)
    {
        return ($this->updated_at + $seconds) > time();
    }
    
    public static function findSmsUniversalByUser($user_id)
    {
        return self::find()
            ->byAction(ConfirmTokenAction::UNIVERSAL)
            ->byType(ConfirmTokenType::SMS)
            ->byUserId($user_id)
            ->limit(1)
            ->one();
    }
}
