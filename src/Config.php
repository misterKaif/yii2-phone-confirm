<?php

namespace Teimur\YiiPhoneConfirm;

use Webmozart\Assert\Assert;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 *
 *
 *
 *
 */
class Config extends Component
{
    public $user;
    
    public $table = '{{%user}}';
    
    public $field = 'phone';
    
    public function init()
    {
        parent::init();
        
        if(empty($user))
            Assert::notEmpty($this->user);
        
        
    }
    
    public static function getUserClass()
    {
        return \Yii::$app->phoneConfirm->user;
    }
    
    public static function getUser()
    {
        $user = Config::getUser();
        
        return Yii::createObject($temp->user);
    }
    

}
