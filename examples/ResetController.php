<?php

//namespace frontend\controllers;

//use common\services\user\AuthService;
//use common\services\user\ResetService;
//use common\services\user\UserService;
use Teimur\YiiPhoneConfirm\forms\ConfirmPhoneForm;
use Teimur\YiiPhoneConfirm\forms\PhoneSetPasswordForm;
use Teimur\YiiPhoneConfirm\forms\ResetByPhoneForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class ResetController extends Controller
{
    private $_user;
    private $resetService;
    private $userService;
    private $authService;
    
    public function __construct($id,
        $module,
        ResetService $resetService,
        UserService $userService,
        AuthService $authService,
        array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_user = Yii::$app->user;
        $this->resetService = $resetService;
        $this->userService = $userService;
        $this->authService = $authService;
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme':null,
            ],
        ];
    }
    
    /**
     * Displays homepage.
     * @return mixed
     */
    public function actionPhone()
    {
        if (!$this->_user->isGuest)
            return $this->goHome();
        
        $form = new ResetByPhoneForm();
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $item = $this->resetService->resetByPhone($form);
            return $this->redirect(['/reset/phone-confirm', 'phone' => $form->phone]);
        } else {
            return $this->render('phone', [
                'model' => $form,
            ]);
        }
        
    }
    
    public function actionPhoneConfirm()
    {
        $form = new ConfirmPhoneForm([
            'phone' => Yii::$app->request->get('phone')
        ]);
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            return $this->redirect(['/reset/phone-set-password', 'phone' => $form->phone, 'token' => $form->token]);
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
            $this->resetService->deleteToken($form->phone, $form->token);
            $user = $this->userService->setPasswordByPhone($form->phone, $form->password);
            $this->authService->login($user);
            return  $this->redirect(['/']);
        } else {
            return $this->render('phoneSetPassword', [
                'model' => $form,
            ]);
        }
    }
}
