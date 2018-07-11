<?php



use common\services\user\AuthService;
use common\services\user\ResetService;
use common\services\user\UserService;
use Teimur\YiiPhoneConfirm\ConfirmTokenService;
use Teimur\YiiPhoneConfirm\forms\ConfirmPhoneForm;
use Teimur\YiiPhoneConfirm\forms\PhoneSetPasswordForm;
use Teimur\YiiPhoneConfirm\forms\SendSmsByPhoneForm;
use Teimur\YiiPhoneConfirm\SmsService;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class PhoneController extends Controller
{
    private $authService;
    private $smsService;
    private $resetService;
    private $userService;
    /**
     * @var ConfirmTokenService
     */
    private $tokenService;
    private $_user;
    
    public function __construct($id,
        $module,
        AuthService $authService,
        SmsService $smsService,
        ResetService $resetService,
        UserService $userService,
        ConfirmTokenService $tokenService,
        array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_user = Yii::$app->user;
        $this->authService = $authService;
        $this->smsService = $smsService;
        $this->resetService = $resetService;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
        ];
    }
    
    public function actionPhoneConfirm()
    {
        $form = new ConfirmPhoneForm([
            'phone' => Yii::$app->request->get('phone')
        ]);
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            return $this->redirect(['/phone/phone-set-password', 'phone' => $form->phone, 'token' => $form->token]);
        } else {
            return $this->render('phoneConfirm', [
                'model' => $form,
            ]);
        }
    }
    
    public function actionPhoneSetPassword()
    {
        $form = new PhoneSetPasswordForm([
            'phone' => Yii::$app->request->get('phone'),
            'token' => Yii::$app->request->get('token'),
        ]);
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $user = $this->userService->confirmUserAfterRegistration($form->phone, $form->password);
            $this->authService->login($user);
            return $this->redirect(['/']);
        } else {
            return $this->render('phoneSetPassword', [
                'model' => $form,
            ]);
        }
    }
    
    public function actionPhoneResend()
    {
        $form = new SendSmsByPhoneForm([
            'phone' => Yii::$app->request->get('phone'),
        ]);
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            
            $this->resetService->resendSmsByPhone($form->phone);
            $user = $this->userService->setPasswordByPhone($form->phone, $form->password);
            $this->authService->login($user);
            
            return $this->redirect(['/']);
        } else {
            return $this->render('phoneResend', [
                'model' => $form,
            ]);
        }
    }
    
    public function actionReset()
    {
        if (!$this->_user->isGuest)
            return $this->goHome();
    
        $form = new SendSmsByPhoneForm();
    
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->resetService->resetByPhone($form);
            return $this->redirect(['/phone/phone-confirm', 'phone' => $form->phone, 'action' => 'reset']);
        } else {
            return $this->render('phoneReset', [
                'model' => $form,
            ]);
        }
    }
}
