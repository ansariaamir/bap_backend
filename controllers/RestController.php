<?php

namespace app\controllers;


use agielks\yii2\jwt\JwtBearerAuth;
// Use your own login form
use common\models\LoginForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use app\models\Token;
use app\models\Helper;

use app\models\transactions\TApplicationSubmission;
use app\models\ServiceConfigParameterMapping;
use app\models\ProjectConfigurations;
use app\models\masters\OptionValue;
use app\models\masters\MstUserrole;
/**
 * Class SiteController
 */

header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
         
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
         
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
         
            exit(0);
        }

class RestController extends Controller
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

    public function actionTest(){
        return Yii::$app->user->identity;
    }

    /**
     * @return array|LoginForm
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        

        
        $model = new \app\models\LoginForm();
       
        
        $model->username = $_POST['username'];
        $model->password = $_POST['password'];
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            $user = $model->getUser();
            $token = Token::tokenGenerator($user->id);
            $prifle_img_url = Helper::GetProfileImgurl($user);
            // $options_sql = "SELECT * FROM mst_option ORDER BY id DESC";
            // $options = Yii::$app->db->createCommand($options_sql)->queryAll();
            $other_modules_array = [];
            $projectDetails = ProjectConfigurations::find()->one();
            if($projectDetails){
        
                $other_modules = explode(',', $projectDetails->other_modules);

                foreach ($other_modules as $value) {
                    $ov = OptionValue::findOne($value);
                    $other_modules_array[$value] = $ov->name;
                }
            }

          
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $server_name = $_SERVER['SERVER_NAME']; 
            $server_port = $_SERVER['SERVER_PORT']; 
            if ($server_port != '80' && $server_port != '443') { 
                $full_address = $protocol . '://' . $server_name . ':' . $server_port;
            } else {
                $full_address = $protocol . '://' . $server_name;
            }


            return [
                        'status' => true,
                        'message' => 'Login successfully',
                        'user_data' => [
                            'user_detail'=>User::GetUserDetails($user->id),
                            'login_time'=>date('Y-m-d H:i:s'),
                            'user_image'=>$prifle_img_url
                        ], 
                        'other_modules' => $other_modules_array,
                        'clientlogo' => ($full_address.'/img/logo.png'),
                        'clientlogocolor' => 1,
                        //'master_options'=> $options,                        
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

    public function actionDashboard(){
         $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            $role_details =  Yii::$app->user->identity;
           
            if(isset($role_details)){
                if($role_details['role_type']=='BO'){
                    $table_format = [
                            'heading'=>'Pending Application',
                            'table_thead'=>['app_id'=>'App ID','applicant'=>'Applicant','department'=>'Department','service'=>'Service','action_on'=>'Action On']
                        ];
                        $data = [
                            'application_data'=>$this->applicationDetails($role_details['role_type'],$role_details['role_id']),
                            'table_format'=>$table_format
                        ];
                }else{
                    $table_format = [
                            'heading'=>'Application Summary',
                            'table_thead'=>['app_id'=>'App ID','department'=>'Department','service'=>'Service','status'=>'Status','action_on'=>'Action On']
                        ];
                        $data = [
                            'application_data'=>$this->applicationDetails($role_details['role_type'],$role_details['role_id'],Yii::$app->user->id),
                            'table_format'=>$table_format
                        ];   
                }

                // $pending_application_on_dept = Yii::$app->db->createCommand("CALL pending_application()")->queryAll();
                return [
                    'status' => true,
                    'user_id' => Yii::$app->user->id,
                    'role_id' => $role_details['role_id'],
                    'role_name' => $role_details['role_name'],
                    'role_type' => $role_details['role_type'],
                    'data'=>$data,
                    // 'pending_application_on_dept' => $pending_application_on_dept,
                    'token' => $token,      
                    
                ];
            }else{
                return [
                    'status' => false,
                    'message'=>'user role is missing',
                    'token' => $token,      
                    
                ];
            }       
        }else{
             return [
                'status' => false, 'message'=>'Session expired. Please login again'
            ];
        } 
    }

    

    protected function applicationDetails($role_type,$role_id,$user_id=NULL,$status=NULL){
        if($user_id){
            $user_id_condition = "AND a.sso_user_id = ".$user_id;
            $role_id_condition = "";
        }else{
            $user_id_condition = "";
            $role_id_condition = "AND a.where_app_is_role_id=".$role_id;
        }



        if($role_type=='BO'){
            $counts = Yii::$app->db->createCommand("SELECT 
            SUM(IF(a.where_app_is_role_id= $role_id, 1, 0)) AS pending,
            SUM(IF(a.application_status = 'A', 1, 0)) AS approved,
            SUM(IF(a.application_status = 'R', 1, 0)) AS rejected
            FROM t_application_submission a
            WHERE a.is_active=1 
            ")->queryOne();
        }else{
            $counts = Yii::$app->db->createCommand("SELECT 
            SUM(IF(a.application_status IN ('D','H'), 1, 0)) AS draft,
            SUM(IF(a.application_status = 'P', 1, 0)) AS pending,
            SUM(IF(a.application_status = 'A', 1, 0)) AS approved,
            SUM(IF(a.application_status = 'R', 1, 0)) AS rejected
            FROM t_application_submission a
            WHERE a.is_active=1 $user_id_condition $role_id_condition
            ")->queryOne();
            if($status){
                $status_condition = "AND a.application_status='".$status."'";
            }else{
                 $status_condition = "AND a.application_status='P'";
            }
        }
        

        

        $applications = Yii::$app->db->createCommand("SELECT 
            a.id as app_id, 
            a.sso_user_id as sso_user_id,
            u.name as applicant,
            d.dept_name as department, 
            s.service_name as service, 
            a.application_status as status, 
            a.created_on as action_on
            FROM t_application_submission a
            INNER JOIN service_config_parameter_mapping scpm ON a.scpm_id=scpm.id
            INNER JOIN mst_services s ON scpm.service_id = s.id
            INNER JOIN mst_departments d ON scpm.dept_id = d.id
            INNER JOIN users u ON a.sso_user_id = u.id
            WHERE a.is_active=1 $user_id_condition $role_id_condition
            ")->queryAll();

        return ['counts'=>$counts,'applications'=>$applications];
    }

    public function actionServiceDashboard(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
                $scpm_query = ServiceConfigParameterMapping::find()->where(['is_workflow_done'=>1,'is_active'=>1])->all(); 
                $data = [];
                if(is_array($scpm_query) && !empty($scpm_query)){
                    foreach ($scpm_query as $key => $value) {
                        $data[] = [
                            'scpm_id'=>$value->id,
                            'dept_id'=>$value->dept_id,
                            'dept_name'=>$value->dept->dept_name,
                            'service_id'=>$value->service_id,
                            'service_name'=>$value->service->service_name,
                            'service_desc'=>$value->service->service_desc,
                            'entity_id'=>$value->entity_id,
                            'entity_name'=>$value->entity_id ? $value->entity->name : 'NA',
                            'is_payment_service'=>$value->is_payment_service,
                            'remark'=>$value->remark,
                        ];
                    }
                }

                return $this->asJson([
                    'status' => true,
                    'data' => $data,
                    'token' => $token,      
                    
                ]);
               
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
    }

    public function actionSentwhatsapp(){       
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
                $message = Helper::Sentwhatsapp('91','7276492239');
                

                return $this->asJson([
                    'status' => true,
                    'message' => $message['message'],
                    'token' => $token,      
                    
                ]);
               
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        }
    }

    public function actionSentmail(){       
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['e_id'])){
                $message = Helper::SendMail($_POST['e_id']);
                return $this->asJson([
                    'status' => true,
                    'message' => $message,
                    'token' => $token,      
                    
                ]);
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing',
                    'token' => $token,      
                    
                ]);
            }               
               
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        }
    }


    public function actionProjectconfigure(){
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['current_step'])){
                $stepArray = $this->stepArray($_POST['current_step']);
                $model = ProjectConfigurations::find()->one();
                if(!$model){
                    $model = new ProjectConfigurations;
                    $model->country_id = 742;
                }
                $step_data = $this->getStepWiseFormData($_POST['current_step'],$model);            

                return $this->asJson([
                    'status' => true,
                    'stepArray'=>$stepArray,
                    'step_data' => $step_data,
                    'model' => $model,
                    'token' => $token            
                ]);
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing',
                    'token' => $token,      
                    
                ]);
            }               
               
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        }
    }

    public function actionSaveProjectConfig(){
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['current_step'])){
                 $model = ProjectConfigurations::find()->one();
                if(!$model){
                    $model = new ProjectConfigurations;
                }

                //return $_POST;
                switch ($_POST['current_step']) {
                    case '1':
                       
                       $model->country_id = $_POST['country_id'];
                       $model->entity_ids = $_POST['entity_id'];
                       $model->state_id = null;
                       if($model->entity_ids){
                        $entitys =  explode(',', $model->entity_ids);
                           if(in_array(915, $entitys)){
                               $model->state_id = $_POST['state_id'];
                            }                        
                       }
                       
                       
                       if(!$model->save()){
                            $errors = [];
                            foreach ($model->errors as $key => $value){
                                $errors[] = $value;
                            }
                             
                             return $this->asJson([
                                'status' => false,
                                'token' => $token,
                                'message' => 'Validation Error',
                                'errors'=>$errors           
                            ]); 

                       }                        
                        break;

                    case '2':
                       $model->dms_type_id = $_POST['dms_type_id'];
                       $model->cms_in_id = $_POST['cms_in_id'];
                       $model->dashboards_id = $_POST['dashboards_id'];
                       $model->mis_reports_ids = $_POST['mis_reports_ids'];      
                       $model->save();                        
                        break;

                    case '3':
                       $model->integrations_with_ids = $_POST['integrations_with_ids'];
                       $model->notifications_services_ids = $_POST['notifications_services_ids'];                            
                       if(!$model->save()){
                        $errors = [];
                            foreach ($model->errors as $key => $value){
                                $errors[] = $value;
                            }
                             
                             return $this->asJson([
                                'status' => false,
                                'token' => $token,
                                'message' => 'Validation Error',
                                'errors'=>$errors           
                            ]);
                       }                        
                        break;
                    
                    case '4':
                       $model->help_desks_ids = $_POST['help_desks_ids'];
                       $model->certificate_verification_ids = $_POST['certificate_verification_ids'];
                       $model->comman_features_ids = $_POST['comman_features_ids'];
                       $model->template_design_ids = $_POST['template_design_ids'];
                       $model->bo_workflow = $_POST['bo_workflow'];
                       $model->other_modules = $_POST['other_modules_ids'];
                       $model->save();           
                        $this->AddDefaultUsers();                       
                        break;

                    case '5':
                       $model->is_mobile_responsive = $_POST['is_mobile_responsive'];
                       $model->auditlog_sla_monitor = $_POST['auditlog_sla_monitor'];
                       $model->cyber_security_compliant = $_POST['cyber_security_compliant'];
                       //$model->workflow = $_POST['workflow'];                          
                       $model->save();      

                        break;                                

                    default:
                        // code...
                        break;
                }
                                              
                 return $this->asJson([
                    'status' => true,
                    'next_step' => $_POST['current_step']+1,
                    'token' => $token            
                ]); 

            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing current step',
                    'token' => $token,      
                    
                ]);
            }
                       
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        }
    }

    protected function getStepWiseFormData($current_step,$model){
        switch ($current_step) {
            case '1':
                
                if($model->entity_ids){
                    $entity_ids = explode(',', $model->entity_ids);
                    $entity_ids = array_map('intval', $entity_ids);    
                }else{
                    $entity_ids = [];       
                }
                
                $step_data = [
                    'current_step' => $current_step,
                    'title' => 'Select Entity',
                    'country_array' => OptionValue::GetOptionValue(1),
                    'entity_array' => OptionValue::GetOptionValue(3),
                    'state_array' => OptionValue::GetOptionValue(NULL,$model->country_id),
                    'selected_data' => [
                        'country_id' => $model->country_id ,
                        'entity_id' => $entity_ids,
                        'state_id' => $model->state_id
                     ],
                     'data_for_view'=>[]
                ];
                break;
            case '2':
                
                if($model->mis_reports_ids){
                    $mis_reports_ids = explode(',', $model->mis_reports_ids);
                    $mis_reports_ids = array_map('intval', $mis_reports_ids);    
                }else{
                    $mis_reports_ids = [];       
                }

                //below code is for checkbox data with selected
                $mis_reports_array_checkbox = $this->DataforCheckbox(11,$mis_reports_ids);
                
                
                $step_data = [
                    'current_step' => $current_step,
                    'title' => 'Main Module',
                    'dms_type_array' => OptionValue::GetOptionValue(8),
                    'cms_array' => OptionValue::GetOptionValue(9), 
                    'dashboard_array' => OptionValue::GetOptionValue(10),
                    'mis_reports_array' => $mis_reports_array_checkbox,              
                    'selected_data' => [
                        'dms_type_id' => $model->dms_type_id ,
                        'cms_in_id' => $model->cms_in_id,
                        'dashboards_id' => $model->dashboards_id,
                        'mis_reports_ids' => $mis_reports_ids
                     ],
                     
                     'data_for_view'=>[
                        'country' => OptionValue::getValueByID($model->country_id),
                        'entity' => OptionValue::getValueByIDs($model->entity_ids),
                        'state' => OptionValue::getValueByID($model->state_id),
                     ]
                ];
                break; 

            case '3':
                
                if($model->integrations_with_ids){
                    $integrations_with_ids = explode(',', $model->integrations_with_ids);
                    $integrations_with_ids = array_map('intval', $integrations_with_ids);    
                }else{
                    $integrations_with_ids = [];       
                }
                $integration_array_checkbox = $this->DataforCheckbox(12,$integrations_with_ids);



                if($model->notifications_services_ids){
                    $notifications_services_ids = explode(',', $model->notifications_services_ids);
                    $notifications_services_ids = array_map('intval', $notifications_services_ids);    
                }else{
                    $notifications_services_ids = [];       
                }
                $notification_array_checkbox = $this->DataforCheckbox(13,$notifications_services_ids);
                
                
                $step_data = [
                    'current_step' => $current_step,
                    'title' => 'External Integrations',
                    'integration_array' => $integration_array_checkbox,
                    'notification_array' => $notification_array_checkbox, 
                    'selected_data' => [
                        'integrations_with_ids' => $integrations_with_ids,
                        'notifications_services_ids' => $notifications_services_ids
                     ],
                     
                     'data_for_view'=>[
                        'country' => OptionValue::getValueByID($model->country_id),
                        'entity' => OptionValue::getValueByIDs($model->entity_ids),
                        'state' => OptionValue::getValueByID($model->state_id),
                     ]
                ];
                break;  

            case '4':
                
                if($model->help_desks_ids){
                    $help_desks_ids = explode(',', $model->help_desks_ids);
                    $help_desks_ids = array_map('intval', $help_desks_ids);    
                }else{
                    $help_desks_ids = [];       
                }
                $help_desks_array_checkbox = $this->DataforCheckbox(14,$help_desks_ids);



                if($model->certificate_verification_ids){
                    $certificate_verification_ids = explode(',', $model->certificate_verification_ids);
                    $certificate_verification_ids = array_map('intval', $certificate_verification_ids);    
                }else{
                    $certificate_verification_ids = [];       
                }
                $certificate_verification_array_checkbox = $this->DataforCheckbox(15,$certificate_verification_ids);

                if($model->comman_features_ids){
                    $comman_features_ids = explode(',', $model->comman_features_ids);
                    $comman_features_ids = array_map('intval', $comman_features_ids);    
                }else{
                    $comman_features_ids = [];       
                }
                $comman_features_array_checkbox = $this->DataforCheckbox(16,$comman_features_ids);

                if($model->template_design_ids){
                    $template_design_ids = explode(',', $model->template_design_ids);
                    $template_design_ids = array_map('intval', $template_design_ids);    
                }else{
                    $template_design_ids = [];       
                }
                $template_design_array_checkbox = $this->DataforCheckbox(17,$template_design_ids);

                //Other Module
                
                if($model->other_modules){
                    $other_modules_ids = explode(',', $model->other_modules);
                    $other_modules_ids = array_map('intval', $other_modules_ids);    
                }else{
                    $other_modules_ids = [];       
                }
                $other_modules_array_checkbox = $this->DataforCheckbox(28,$other_modules_ids);
                
                
                $step_data = [
                    'current_step' => $current_step,
                    'title' => 'Other Features',
                    'help_desks_array' => $help_desks_array_checkbox,
                    'certificate_verification_array' => $certificate_verification_array_checkbox,
                    'comman_features_array' => $comman_features_array_checkbox,
                    'template_design_array' => $template_design_array_checkbox, 
                    'bo_workflow_array' => Helper::GetStaticYesNo(),
                    'other_modules_array' => $other_modules_array_checkbox,
                    'selected_data' => [
                        'help_desks_ids' => $help_desks_ids,
                        'certificate_verification_ids' => $certificate_verification_ids,
                        'comman_features_ids' => $comman_features_ids,
                        'template_design_ids' => $template_design_ids,
                        'bo_workflow' => $model->bo_workflow,
                        'other_modules_ids' => $other_modules_ids
                     ],
                     
                    'data_for_view'=>[
                        'country' => OptionValue::getValueByID($model->country_id),
                        'entity' => OptionValue::getValueByIDs($model->entity_ids),
                        'state' => OptionValue::getValueByID($model->state_id),
                     ]
                ];
                break;

            case '5':
                
                $step_data = [
                    'current_step' => $current_step,
                    'title' => 'Main Module',
                    'yes_no_array' => [['id'=>1,'name'=>'Yes'],['id'=>0,'name'=>'No']],
                    'selected_data' => [
                        'is_mobile_responsive' => $model->is_mobile_responsive ,
                        'auditlog_sla_monitor' => $model->auditlog_sla_monitor,
                        'cyber_security_compliant' => $model->cyber_security_compliant,
                        //'workflow' => $model->workflow
                     ],
                     
                     'data_for_view'=>[
                        'country' => OptionValue::getValueByID($model->country_id),
                        'entity' => OptionValue::getValueByIDs($model->entity_ids),
                        'state' => OptionValue::getValueByID($model->state_id),
                     ]
                ];
                break;              
            
            default:
                $step_data = [];
                break;
        }
        return  $step_data;       
    }
    

    protected function stepArray($current_step){        
       $step_array = [
            ['id'=>1,'name'=>'Select Entity'],
            ['id'=>2,'name'=>'Main Module'],
            ['id'=>3,'name'=>'External Integrations'],
            ['id'=>4,'name'=>'Other Features'],
           // ['id'=>5,'name'=>'Extra']
        ];
       $width = 100/sizeof($step_array);
       $current_step_array = [];  
        foreach ($step_array as $value) {
            
            if((int)$current_step==$value['id']){
                $bg = '';
            }else{
                 $bg = 'bg-secondary';
            }
            $current_step_array[]= [
                    'id'=> $value['id'],
                    'current_step'=>(int)$current_step,
                    'name'=> $value['name'], 
                    'width'=> $width,
                    'bg'=>$bg,
                    'total_step'=>sizeof($step_array)
                ];
        } 
        return  $current_step_array;
    }

    protected function DataforCheckbox($option_id,$default_data){
        $OptionValue = OptionValue::GetOptionValue($option_id);
        $data = [];
        foreach ($OptionValue as $key => $value) {
            if(in_array($value->id, $default_data)){
                $selected = true;
            }else{
                $selected = false;
            }
            $data[] = [
                'id'=>$value->id,
                'name'=>$value->name,
                'selected' =>$selected
            ];
        }

        return $data;        
    }

    protected function AddDefaultUsers(){
        $cdate = date('Y-m-d H:i:s');
        $cuser_id = Yii::$app->user->id;
        // first check then create Developer account
        $user_exist = Yii::$app->db->createCommand("SELECT * FROM users u 
            INNER JOIN user_profile up ON up.user_id = u.id
            INNER JOIN mst_userrole mur ON mur.id = up.role_id
            WHERE mur.role_type = 'DU' AND mur.role_name='developer' AND u.status=1
            ")->queryAll();
        if(empty($user_exist)){
            $find_mstuserRole = MstUserrole::find()->where(['role_type'=>'DU','role_name'=>'developer'])->one();
            if($find_mstuserRole){
                $murModel = $find_mstuserRole;
            }else{
                $murModel = new MstUserrole;
                $murModel->role_type = 'DU';
                $murModel->role_name = 'developer';
                $murModel->role_name_label = 'Developer';
                $murModel->short_code = 'DEV';
                $murModel->created_on = $cdate;
                $murModel->is_active = 1;
                $murModel->save();
            }

            $dev_acc_count = Yii::$app->params['dev_accounts_count'];
            // if($dev_acc_count=='' || $dev_acc_count==0){
            //     $dev_acc_count = 1;
            // }
            for ($i=1; $i <= $dev_acc_count ; $i++) { 
                
                

                $userModel = new User;
                $userModel->email = 'dev'.$i.'@bap.com';
                $userModel->name = 'Dev '.$i;
                $userModel->password_hash = Yii::$app->security->generatePasswordHash(123456);
                $userModel->auth_key = Yii::$app->security->generateRandomString();
                $userModel->mobile_no = 0000000001;
                $userModel->status = 1;
                $userModel->created_at = $cdate;
                $userModel->created_by = $cuser_id;
                $userModel->updated_at = $cdate;
                $userModel->updated_by = $cuser_id;
                

                if($userModel->save()){
                    Yii::$app->db->createCommand()
                    ->insert('user_profile', [
                        'user_id' => $userModel->id,
                        'role_id' => $murModel->id,
                        'is_default' => 1,
                        'created_at' => $cdate,
                        'created_by' => $cuser_id,
                        'updated_at' => $cdate,
                        'updated_by' => $cuser_id,
                    ])
                    ->execute();
                }                
            }
        }

        // then check client admin role_type BO & role_name admin
        $ca_exist = Yii::$app->db->createCommand("SELECT * FROM users u 
            INNER JOIN user_profile up ON up.user_id = u.id
            INNER JOIN mst_userrole mur ON mur.id = up.role_id
            WHERE mur.role_type = 'BO' AND mur.role_name='admin' AND u.status=1
            ")->queryOne();
        if(empty($ca_exist)){
            $find_mstuserRole = MstUserrole::find()->where(['role_type'=>'BO','role_name'=>'admin'])->one();
            if($find_mstuserRole){
                $murModel = $find_mstuserRole;
            }else{
                $murModel = new MstUserrole;
                $murModel->role_type = 'BO';
                $murModel->role_name = 'admin';
                $murModel->role_name_label = 'Admin';
                $murModel->short_code = 'DEV';
                $murModel->created_on = date('Y-m-d H:i:s');
                $murModel->is_active = 1;
                $murModel->save();
            }

            $userModel = new User;
            $userModel->email = 'admin@'.Yii::$app->params['client_domain'];
            $userModel->name = 'Admin';
            $userModel->password_hash = Yii::$app->security->generatePasswordHash(123456);
            $userModel->auth_key = Yii::$app->security->generateRandomString();
            $userModel->mobile_no = 0000000001;
            $userModel->status = 1;
            $userModel->created_at = $cdate;
            $userModel->created_by = $cuser_id;
            $userModel->updated_at = $cdate;
            $userModel->updated_by = $cuser_id;
            if($userModel->save()){
                Yii::$app->db->createCommand()
                ->insert('user_profile', [
                    'user_id' => $userModel->id,
                    'role_id' => $murModel->id,
                    'is_default' => 1,
                    'created_at' => $cdate,
                    'created_by' => $cuser_id,
                    'updated_at' => $cdate,
                    'updated_by' => $cuser_id,
                ])
                ->execute();
            } 
        }

        return true;
    }
    

}
