<?php

namespace teimur8\yiiPhoneConfirm;

use Webmozart\Assert\Assert;
use Yii;
use yii\base\Component;
use yii\base\Security;

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

    public function generate(User $user, int $type, int $action): ConfirmToken
    {
        $code = new ConfirmToken([
            'user_id' => $user->id,
            'type' => $type,
            'action' => $action,
            'token' => YII_DEBUG || YII_ENV_TEST ? 123456 : rand(111111, 999999),
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
}
