<?php

namespace Teimur\YiiPhoneConfirm;

use api\exceptions\Http400Exception;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenAction;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenType;
use Teimur\YiiPhoneConfirm\entities\ConfirmToken;
use Webmozart\Assert\Assert;
use Yii;
use yii\base\Component;
use yii\base\Security;
use yii\web\HttpException;

class ConfirmTokenService extends Component
{
    /**
     * @var Security
     */
    private $security;
    
    /**
     * @inheritDoc
     */
    public function __construct(Security $security, $config = [])
    {
        $this->security = $security;
        parent::__construct($config);
    }
    
    public function generate($user_id, int $type, int $action): ConfirmToken
    {
        $this->deleteOldToken($action, $type, $user_id);
        
        $item = $this->validateToken($action, $type, $user_id);
        if ($item)
            return $item;
        
        $code = new ConfirmToken([
            'user_id'    => $user_id,
            'type'       => $type,
            'action'     => $action,
            'token'      => YII_DEBUG || YII_ENV_TEST ? 123456:rand(111111, 999999),
            'expires_at' => strtotime('+1 day'),
        ]);
        
        Assert::true($code->save(false));
        
        return $code;
    }
    
    public function regenerate(ConfirmToken $oldToken): ConfirmToken
    {
        $tr = Yii::$app->db->beginTransaction();
        Assert::integer($oldToken->delete());
        $newToken = $this->generate($oldToken->user, $oldToken->type, $oldToken->action);
        $tr->commit();
        
        return $newToken;
    }
    
    /**
     * Set attempt number of all active confirm tokens to 0
     */
    public function resetCounters()
    {
        ConfirmToken::updateAll([
            'attemptNo' => 0
        ]);
    }
    
    public function checkToken($action, $type, $user_id, $token)
    {
        return ConfirmToken::find()
            ->byAction($action)
            ->byType($type)
            ->byUserId($user_id)
            ->byToken($token)
            ->one();
    }
    
    public function findUserToken($action, $type, $user_id)
    {
        return ConfirmToken::find()
            ->byUserId($user_id)
            ->byType($type)
            ->byAction($action)
            ->one();
    }
    
    public function deleteOldToken()
    {
        ConfirmToken::deleteAll(['<', 'expires_at', time()]);
    }
    
    public function validateToken($action, $type, $user_id)
    {
        $item = $this->findUserToken($action, $type, $user_id);
        
        if (empty($item))
            return null;
        
        $this->attemptCheck($item);
        
        return $item;
    }
    
    public function attemptCheck(ConfirmToken $token)
    {
        if ($token->attempt_no < 3 && $token->wait(60 * 2))
            throw new HttpException(400, Yii::t('app', 'It is too soon to resend a new token of this type. Wait 2 minutes.'));
        
        if ($token->attempt_no >= 3 && $token->attempt_no <= 5 && $token->wait(60 * 5))
            throw new HttpException(400, Yii::t('app', 'It is too soon to resend a new token of this type. Wait 5 minutes.'));
        
        if ($token->attempt_no >= 5)
            throw new HttpException(400, Yii::t('app', 'Too many attempts. Try tomorrow.'));
        
        
        $token->updateCounters(['attempt_no' => 1]);
    }
    
    public function findUserSmsToken($user_id)
    {
        $this->deleteOldToken();
        
        return ConfirmToken::find()
            ->byType(ConfirmTokenType::SMS)
            ->byAction(ConfirmTokenAction::UNIVERSAL)
            ->byUserId($user_id)
            ->notExpiried()
            ->one();
    }
    
    public function findUserEmailToken($user_id)
    {
        $this->deleteOldToken();
        
        return ConfirmToken::find()
            ->byType(ConfirmTokenType::EMAIL)
            ->byAction(ConfirmTokenAction::UNIVERSAL)
            ->byUserId($user_id)
            ->notExpiried()
            ->one();
    }
    
    public function validatePhone($phone)
    {
        $user = \Teimur\YiiPhoneConfirm\entities\User::byPhone($phone);
    
        if(empty($user))
            throw new HttpException(400, Yii::t('app', 'Try to sign up'));
    
        $token = $this->findUserSmsToken($user->id);
    
        $try_count = 10;
    
        if (empty($token))
            return null;
    
        if ($token->try_count > $try_count)
            throw new HttpException(400, Yii::t('app', 'Too many try. Try tomorrow.'));
        
        $this->attemptCheck($token);
        
        return $token;
    }
    
    public function validateSmsToken($phone, $code = null)
    {
        $user = \Teimur\YiiPhoneConfirm\entities\User::byPhone($phone);
    
        if(empty($user))
            throw new HttpException(400, Yii::t('app', 'Try to sign up'));
    
        $token = $this->findUserSmsToken($user->id);
    
        $try_count = 10;
    
        if (empty($token))
            throw new HttpException(400, Yii::t('app', 'Try to resend sms'));
    
        if ($token->try_count > $try_count)
            throw new HttpException(400, Yii::t('app', 'Too many try. Try tomorrow.'));
    
        if ($token->token != $code) {
            $token->updateCounters(['try_count' => 1]);
            throw new HttpException(
                400,
                Yii::t('app', 'Incorrect code. Try again. There are still {count} attempts ', ['count' => $try_count - $token->try_count])
            );
        }
        
        return $token;
    }
    
    public function validateEmailToken($user_id, $code)
    {
        $user = \Teimur\YiiPhoneConfirm\entities\User::find()
            ->where(['id' => $user_id])
            ->one();
        
    
        if(empty($user))
            throw new HttpException(400, Yii::t('app', 'Try to sign up'));
    
        $token = $this->findUserEmailToken($user->id);
        
        $try_count = 10;
    
        if (empty($token))
            throw new HttpException(400, Yii::t('app', 'Try to resend email'));
    
        if ($token->try_count > $try_count)
            throw new HttpException(400, Yii::t('app', 'Too many try. Try tomorrow.'));
    
        if ($token->token != $code) {
            $token->updateCounters(['try_count' => 1]);
            throw new HttpException(
                400,
                Yii::t('app', 'Incorrect code. Try again. There are still {count} attempts ', ['count' => $try_count - $token->try_count])
            );
        }
    
        return $token;
        
    }
    
    public function validateSmsTokenByUserId($user_id, $code)
    {
        $user = \Teimur\YiiPhoneConfirm\entities\User::find()
            ->where(['id' => $user_id])
            ->one();
    
        if(empty($user))
            throw new HttpException(400, Yii::t('app', 'Try to sign up'));
    
        $token = $this->findUserSmsToken($user->id);
        
        $try_count = 10;
    
        if (empty($token))
            throw new HttpException(400, Yii::t('app', 'Try to resend sms'));
    
        if ($token->try_count > $try_count)
            throw new HttpException(400, Yii::t('app', 'Too many try. Try tomorrow.'));
    
        if ($token->token != $code) {
            $token->updateCounters(['try_count' => 1]);
            throw new HttpException(
                400,
                Yii::t('app', 'Incorrect code. Try again. There are still {count} attempts ', ['count' => $try_count - $token->try_count])
            );
        }
    
        return $token;
        
    }
    
    public function deleteToken($phone, $code)
    {
        $user = \Teimur\YiiPhoneConfirm\entities\User::byPhone($phone);
    
        if(empty($user))
            throw new HttpException(400, Yii::t('app', 'User not found'));
    
        $token = $this->findUserSmsToken($user->id);
        if (empty($token))
            throw new HttpException(400, Yii::t('app', 'Token not found'));
        
        if(!$token->delete())
            throw new HttpException(400, Yii::t('app', 'Delete error'));
    }
}
