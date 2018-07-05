<?php

namespace Teimur\YiiPhoneConfirm\entities;

/**
 * This is the ActiveQuery class for [[ConfirmToken]].
 *
 * @see ConfirmToken
 */
class ConfirmTokenQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ConfirmToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ConfirmToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $action
     * @return $this
     */
    public function byAction(int $action)
    {
        return $this->andWhere(compact('action'));
    }

    public function byType(int $type)
    {
        return $this->andWhere(compact('type'));
    }
    
    public function expiried()
    {
        return $this->andWhere(['<', 'expires_at', time()]);
    }
    
    public function notExpiried()
    {
        return $this->andWhere(['>', 'expires_at', time()]);
    }

    /**
     * @param int $user_id
     * @return $this
     */
    public function byUserId(int $user_id)
    {
        return $this->andWhere(compact('user_id'));
    }

    /**
     * @param string $token
     * @return $this
     */
    public function byToken(string $token)
    {
        return $this->andWhere(compact('token'));
    }
    
    

    /**
     * @param string $time
     * @return $this
     */
    public function within(string $time)
    {
        return $this->andWhere(['>', 'updated_at', strtotime("$time ago")]);
    }
}
