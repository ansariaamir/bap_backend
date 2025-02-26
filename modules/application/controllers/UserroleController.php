<?php 
namespace app\modules\application\controllers;


use app\models\Token;
use app\models\Helper;

use Yii;
use yii\web\Response;
use agielks\yii2\jwt\JwtBearerAuth;
use yii\filters\Cors;
use app\modules\application\controllers\RestController;
use app\models\ServiceConfigParameterMapping;
use app\models\ServiceTabMapping;
use app\models\ServiceFormTabSectionMapping;
use app\models\ServiceFormTabSectionFormFieldsMapping;
use app\models\ServiceFormFieldAddMoreMapping;
use app\models\masters\SectionCategory;
use app\models\masters\FormFields;
use app\models\masters\FieldDatatype;
use app\models\ServiceDmsMapping;
use app\models\ServiceDeclarationMapping;
use app\models\masters\Document;
use app\models\masters\OptionValue;
use app\models\transactions\TApplicationSubmission;
use app\models\transactions\TApplicationLog;
use app\models\transactions\TApplicationDms;
use app\models\transactions\TDmsVerification;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use app\models\User;
use app\models\UserProfile;
use app\models\masters\MstUserrole;

class UserroleController extends RestController{

	
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['corsFilter'] = ['class' => Cors::class];
        $behaviors['authenticator'] = [
            'class' => JwtBearerAuth::class,
            
        ];

        return $behaviors;
    }

    


	public function actionIndex(){
        $token = Token::tokenGenerator(Yii::$app->user->id);

        
        
// this is a filters
        if(isset($_POST['role_type']) && Helper::CheckNotEmptyCondition($_POST['role_type'])){
            $role_type_condi = "AND r.role_type ='".$_POST['role_type']."' ";
        }else{
            $role_type_condi = "AND r.role_type ='BO'";
        }

        

        if(isset($_POST['user_role_id']) && Helper::CheckNotEmptyCondition($_POST['user_role_id'])){
            $user_role_condi = "AND r.id=".$_POST['user_role_id'];
        }else{
            $user_role_condi = "";
        }

        if(isset($_POST['department_id']) && Helper::CheckNotEmptyCondition($_POST['department_id'])){
            $dept_condi = "AND dept.id IN (".$_POST['department_id'].")";
        }else{
            $dept_condi = "";
        }
//ENd filters code

// this is for pagination
if(isset($_POST['page_size']) && $_POST['page_size']>0 && is_numeric($_POST['page_size'])){
    $page_size = $_POST['page_size'];
}else{
    $page_size = 20;
}
        
if(isset($_POST['page_id']) && $_POST['page_id']>0 && is_numeric($_POST['page_id'])){
    $page_id = $_POST['page_id'];    
}else{
    $page_id = 1;
}
$offset = ($page_id-1)*$page_size;
// end pagination filter data

        

        $comman_sql = "FROM user_profile up 
                INNER JOIN users u ON up.user_id = u.id
                INNER JOIN mst_userrole r ON up.role_id = r.id
                LEFT JOIN mst_departments dept on up.dept_id = dept.id
                LEFT JOIN mst_option_value state on up.state_id = state.id
                LEFT JOIN mst_option_value district on up.district_id = district.id
                WHERE r.role_type NOT IN ('DU','NA')
                $role_type_condi $user_role_condi $dept_condi";

       
        $count = Yii::$app->db->createCommand("SELECT COUNT(*) ".$comman_sql)->queryScalar();  

        $data = Yii::$app->db->createCommand("SELECT up.id as user_profile_id, u.id as user_id, u.name as user_name, u.email as user_email, u.mobile_no as user_mobile,r.role_type as user_role_type, r.role_name_label as user_role, dept.dept_name as dept_name, state.name as state_name, district.name as district_name,
         CASE u.status
                WHEN 1 THEN 'Active'
                ELSE 'Deactive'
            END AS user_status_label, 
            up.created_at, up.deleted_at ".$comman_sql." ORDER BY u.name ASC Limit $offset,$page_size")->queryAll();
   
        
        return [
                'status'=>true,
                'page_size'=> $page_size,
                'page_id' => $page_id, 
                // 'role_type'=>$role_type,
                // 'userroles'=>$userroles,
                //'departments' => $departments,
                'total_users_count' => $count,
                'data'=>$data,
                'token'=>$token
            ];
	}

    public function actionGetUserDetail(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['user_profile_id'])){
            $model = UserProfile::findOne($_POST['user_profile_id']);
            if($model){
                $data = [
                    'user_profile_id'=>$model->id,
                    'user_id' => $model->user_id,
                    'name'=>$model->user->name,
                    'email'=>$model->user->email,
                    'mobile'=>$model->user->mobile_no,
                    'role_type' => $model->role->role_type,
                    'role_id' => $model->role_id,
                    'role_name' => $model->role->role_name_label,
                    'dep_id' => $model->dept_id,
                    'dept_name' => ($model->dept_id ? $model->department->dept_name : null),
                    'state_id' => $model->state_id,
                    'state' => ($model->state_id ? $model->state->name : null),
                    'district_id' => $model->district_id,
                    'district' => ($model->district_id ? $model->district->name : null),
                    'gender_id' => $model->gender,
                    'gender' => ($model->gender ? OptionValue::getValueByID($model->gender) : null),
                     'dob' => ($model->dob ? date('d-m-Y',strtotime($model->dob)) : null),
                     'full_address' => $model->full_address,
                     'status_label' => ($model->user->status==1 ? "Active" : "Deactive" ),
                     'user_image' => ($model->user->user_image ? $model->user->user_image : "/img/user_avatar.png"),
                     'created_on' => $model->created_at,
                     'updated_on' => $model->updated_at,
                     'deleted_on' => $model->deleted_at,
                     
                     
                ];
                return [
                    'status'=>true,
                    'data' => $data,
                    'message' => 'user detail fetch successfully',
                    'token'=>$token
                ];
            }else{
                return [
                    'status'=>false,
                    'message' =>'no data found',
                    'token'=>$token
                ];
            }
        }else{
            return [
                'status'=>false,
                'message' =>'parameter missing',
                'token'=>$token
            ];
        }
    }

/*
* this action is for add update the user details
*/
    public function actionManageUser(){

    $token = Token::tokenGenerator(Yii::$app->user->id);
    $cuser_id = Yii::$app->user->id;
    $cdate = date('Y-m-d H:i:s');
    if(isset($_POST['name']) && Helper::CheckNotEmptyCondition($_POST['name']) && isset($_POST['email']) && Helper::CheckNotEmptyCondition($_POST['email']) && isset($_POST['mobile']) && Helper::CheckNotEmptyCondition($_POST['mobile'])){

        if(isset($_POST['user_id']) && Helper::CheckNotEmptyCondition($_POST['user_id'])){
            $userModel = User::findOne($_POST['user_id']);
            
            $UserProfile = UserProfile::find()->where(['user_id'=>$userModel->id])->one();
            
        }else{
            $userModel = new User;            
            $userModel->created_at = $cdate;
            $userModel->created_by = $cuser_id;
            

            $UserProfile = new UserProfile; 
            $UserProfile->created_at = $cdate;
            $UserProfile->created_by = $cuser_id;
        }

        $userModel->name = $_POST['name'];
        $userModel->email = $_POST['email'];
        $userModel->mobile_no = $_POST['mobile'];
        //$userModel->user_image = 
        $userModel->updated_at = $cdate;
        $userModel->updated_by = $cuser_id;
        $userModel->save();

        $UserProfile->user_id = $userModel->id;
        $UserProfile->role_id = $_POST['role_id'];
        $UserProfile->dept_id = $_POST['dept_id'];
        $UserProfile->state_id = $_POST['state'];
        $UserProfile->district_id = $_POST['district'];
        $UserProfile->gender = $_POST['gender'];
        $UserProfile->dob = $_POST['dob'];
        $UserProfile->full_address = $_POST['full_address'];
        $UserProfile->updated_at = $cdate;
        $UserProfile->updated_by = $cuser_id;        
        $UserProfile->save();

        return [
                'status'=>true,
                'message' =>'User Data Saved',
                'token'=>$token
            ];

    }else{
         return [
                'status'=>false,
                'message' =>'parameter missing',
                'token'=>$token
            ];
    }    
        

       
        
    }

/*
* By this action admin can deactive or active the users
*/
    public function actionActiveDeactiveUser(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
    }

/*
* this action is for admin also but for admin user_id pass on payload
*/
    public function actionUpdatePassword(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
    }

    
//-----------  Role Masters----------------//
    public function actionGetroledetails(){
         $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['id']) && Helper::CheckNotEmptyCondition($_POST['id'])){
            $model = MstUserrole::findOne($_POST['id']);
        }else{
            $model = null;
        }

        $data = MstUserrole::find()->where(['IN','role_type',['FO','BO']])->all();

        return [
            'status'=>true,
            'message'=>'role master data',
            'model'=>$model,
            'data'=>$data,
            'token' => $token
        ];
    }

    public function actionCreateUpdateRole(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['id']) && Helper::CheckNotEmptyCondition($_POST['id'])){
            $model = MstUserrole::findOne($_POST['id']);
        }else{
            $model = new MstUserrole;
            $model->role_type = 'BO';
            $model->created_on = date("Y-m-d H:i:s");
        }
        $model->role_name_label = $_POST['name'];
        $model->role_name = strtolower(str_replace(' ', '_', $model->role_name_label));
        
        $model->save();
        $data = MstUserrole::find()->where(['IN','role_type',['FO','BO']])->all();
        return [
            'status'=>true,
            'message'=>'role master saved',
            'model'=>$model,
            'data'=>$data,
            'token' => $token
        ];

    }
    
}

?>