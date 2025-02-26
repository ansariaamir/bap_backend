<?php
namespace app\modules\application\controllers;

use agielks\yii2\jwt\JwtBearerAuth;
// Use your own login form
use common\models\LoginForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\Cors;
use app\modules\application\controllers\RestController;
use yii\web\Response;
use app\models\User;
use app\models\Token;
use app\models\Helper;

use app\models\transactions\TApplicationSubmission;
use app\models\ServiceConfigParameterMapping;
use app\models\ProjectConfigurations;
use app\models\masters\OptionValue;
/**
 * Class SiteController
 */
class AuthController extends RestController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['corsFilter'] = ['class' => Cors::class];
        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
           'optional' => [
                'login',
            ],
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'login' => ['OPTIONS', 'POST'],
        ];
    }

    /**
     * @return array|LoginForm
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new \app\models\LoginForm();
        $rawjson = Yii::$app->request->getRawBody();
        if($rawjson){
            $_POST = json_decode($rawjson, true);
        }
        $model->username = $_POST['username'];
        $model->password = $_POST['password'];
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            $user = $model->getUser();
            $userDetail = User::GetUserDetails($user->id);
            if($userDetail['role_type']=='DU'){
                return [
                    'status'=>false,
                    'message' => 'Invalid Credentials'
                ];
            }
            $token = Token::tokenGenerator($user->id);
            $profile_img_url = Helper::GetProfileImgurl($user);
           

            return [
                        'status' => true,
                        'message' => 'Login successfully',
                        'user_data' => [
                            'user_detail'=>$userDetail,
                            'login_time'=>date('Y-m-d H:i:s'),
                            'user_image'=>$profile_img_url
                        ], 
                                             
                        'token' => $token,
                    ];
        }else{
            $model->validate();
            return ['status'=>false,'msg'=>$model];
        }       
    }

   
    public function actionGetidentity(){
        if(Yii::$app->user->id){            
            return [
                'success' => true,
                'identity'=>Yii::$app->user->identity,
            ];
        }else{
             return [
                'success' => false, 'message'=>'Session expired. Please login again'
            ];
        } 
    }

    public function actionLogout(){
        if(Yii::$app->user->id){            
            Yii::$app->user->logout();
            return [
                'status' => true,
            ];
        }else{
             return [
                'status' => false, 'message'=>'Session expired. Please login again'
            ];
        } 
    }

    



}
