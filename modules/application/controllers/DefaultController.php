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
use app\models\masters\Services;

use app\models\transactions\TApplicationSubmission;
use app\models\ServiceConfigParameterMapping;
use app\models\ProjectConfigurations;
use app\models\masters\OptionValue;
/**
 * Class SiteController
 */
class DefaultController extends RestController
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
           
        ];

        return $behaviors;
    }

   

    public function actionDashboard(){
        
            $token = Token::tokenGenerator(Yii::$app->user->id);
            $role_details =  Yii::$app->user->identity;
           
            //status filter code
            $status = isset($_POST['status']) ? $_POST['status'] : null;

            if(isset($_POST['status_label']) && ($_POST['status_label'] != " " || $_POST['status_label'] != null || $_POST['status_label'] != 'null' || $_POST['status_label']!='undefined' || $_POST['status_label'] != undefined || !empty($_POST['status_label']))){
                $status_label = $_POST['status_label'];
                
            }else{
                $status_label = 'Pending';
            }
  
            if(isset($role_details)){
                if($role_details['role_type']=='BO'){

                    switch ($role_details['role_name']) {
                        case 'department':
                            $table_format = [
                            'heading'=>'Pending Application',
                            'table_thead'=>['app_id'=>'App ID','applicant'=>'Applicant','department'=>'Department','service'=>'Service','action_on'=>'Action On']
                            ];

                            $data = [
                                'application_data'=>null,
                                'table_format'=>$table_format
                            ];

                            break;

                        case 'admin':
                            $table_format = [
                            'heading'=>'Pending Application',
                            'table_thead'=>[
                                'app_id'=>'App ID',
                                'department_name'=>'Department',
                                'service_name'=>'Service',
                                'dept_process_started_date'=>'Dept Process Started Date',
                                'end_date'=>'End Date',
                                'time_taken_by_dept'=>'Time Taken By Dept',
                                'service_timeline_maxdays'=>'Total Days'
                                ]
                            ];
                            $data = [
                                'application_data'=> [
                                    'counts'=>null,
                                    'applications'=>Yii::$app->db->createCommand("CALL pending_application()")->queryAll()
                                
                                ],
                                'table_format'=>$table_format
                                
                            ];

                            break;    
                        
                        default:
                            $table_format = [
                            'heading'=>($status_label.' Application'),
                            'table_thead'=>['app_id'=>'App ID',
                            'project_id' => 'Project ID',
                            'applicant'=>'Applicant','service'=>'Schemes','action_on'=>'Action On']
                            ];
                            $data = [
                                'application_data'=>$this->boData($role_details['role_type'],$role_details['role_id'],Yii::$app->user->id,$status),
                                'table_format'=>$table_format
                            ];
                            break;
                    }

                                       
                }else{
                    $table_format = [
                            'heading'=>((($status!='D' ? (TApplicationSubmission::applicationStatus($status)['application_status_label']) : "Submitted")) .' Application Summary'),
                            'table_thead'=>[
                                'app_id'=>'App ID',
                                'project_id' => 'Project ID',
                                'service'=>'Schemes',
                                'status'=>'Status',
                                'action_on'=>($status!='D' ? (TApplicationSubmission::applicationStatus($status)['application_status_label']." On") : "Submitted On")]
                        ];


                        $data = [
                            'application_data'=>$this->foData($role_details['role_type'],$role_details['role_id'],Yii::$app->user->id,$status),
                            'table_format'=>$table_format
                        ];   
                }

                
                return [
                    'status' => true,
                    'user_id' => Yii::$app->user->id,
                    'role_id' => $role_details['role_id'],
                    'role_name' => $role_details['role_name'],
                    'role_type' => $role_details['role_type'],
                    'data'=>$data,
                    'token' => $token,      
                    
                ];
            }else{
                return [
                    'status' => false,
                    'message'=>'user role is missing',
                    'token' => $token,      
                    
                ];
            }       
    }

    
protected function foData($role_type,$role_id,$user_id=NULL,$status=NULL){
    $user_id_condition = "AND a.sso_user_id = ".$user_id;
    $role_id_condition = "";
    $dept_id_condition = "";

    $counts = Yii::$app->db->createCommand("SELECT 
            SUM(IF(a.application_status IN ('D'), 1, 0)) AS D,
            SUM(IF(a.application_status IN ('H'), 1, 0)) AS H,
            SUM(IF(a.application_status IN ('P','F'), 1, 0)) AS P,
            SUM(IF(a.application_status = 'A', 1, 0)) AS A,
            SUM(IF(a.application_status = 'R', 1, 0)) AS R
            FROM t_application_submission a
            WHERE a.is_active=1 $user_id_condition
            ")->queryOne();
    $count_array = [];
    if($counts){
        //$count_array['D'] = ['label'=>'Draft','count'=>$counts['D']];
        $count_array['H'] = ['label'=>'Reverted','count'=>$counts['H']];
        $count_array['P'] = ['label'=>'In Progress','count'=>$counts['P']];
        $count_array['A'] = ['label'=>'Approved','count'=>$counts['A']];
        $count_array['R'] = ['label'=>'Rejected','count'=>$counts['R']];
    }   

            if($status){
                if($status=='P'){
                    $status_condition = "AND a.application_status IN ('P','F')";
                }else{
                    $status_condition = "AND a.application_status='".$status."'";
                }
                
            }else{
                 $status_condition = "";
            }

    $applications = Yii::$app->db->createCommand("SELECT 
            a.id as app_id, 
            CONCAT('EA-NAPDDR-000',a.id) as project_id,
            a.scpm_id as scpm_id,
            a.sso_user_id as sso_user_id,
            u.name as applicant,
            d.dept_name as department, 
            s.service_name as service, 
            a.application_status as status, 
            CASE a.application_status
                WHEN 'D' THEN 'Draft'
                WHEN 'P' THEN 'Pending'
                WHEN 'H' THEN 'Reverted'
                WHEN 'F' THEN 'Pending'
                WHEN 'A' THEN 'Approved'
                WHEN 'R' THEN 'Rejected'
                ELSE 'Unknown'
            END AS status_label,
            a.created_on as action_on
            FROM t_application_submission a
            INNER JOIN service_config_parameter_mapping scpm ON a.scpm_id=scpm.id
            INNER JOIN mst_services s ON scpm.service_id = s.id
            INNER JOIN mst_departments d ON scpm.dept_id = d.id
            INNER JOIN users u ON a.sso_user_id = u.id
            WHERE a.is_active=1 $user_id_condition $role_id_condition $dept_id_condition $status_condition
            ORDER BY a.id DESC
            ")->queryAll();

        return ['counts'=>$count_array,'applications'=>$applications,'total_count'=>sizeof($applications)];        
}


protected function boData($role_type,$role_id,$user_id=null,$status='P'){
   
    $role_id_condition = "AND a.where_app_is_role_id=".$role_id;
    $dept_id_condition = "AND scpm.dept_id =".Yii::$app->user->identity->dept_id;

    $counts = Yii::$app->db->createCommand("SELECT 
            SUM(IF(a.application_status not in ('H','D','A','R') $role_id_condition, 1, 0)) AS P,
            SUM(IF(a.application_status in ('F') AND a.where_app_is_role_id!=$role_id, 1, 0)) AS F,
            SUM(IF(a.application_status = 'A', 1, 0)) AS A,
            SUM(IF(a.application_status = 'R', 1, 0)) AS R
            FROM t_application_submission a
            INNER JOIN service_config_parameter_mapping scpm on a.scpm_id = scpm.id
            WHERE a.is_active=1 
            $dept_id_condition
            ")->queryOne();
    $count_array = [];
    if($counts){

        $count_array['P'] = ['label'=>'Pending','count'=>$counts['P']];
        $count_array['F'] = ['label'=>'Forwarded','count'=>$counts['F']];
        $count_array['A'] = ['label'=>'Approved','count'=>$counts['A']];
        $count_array['R'] = ['label'=>'Rejected','count'=>$counts['R']];
    }  

if($status == null || $status == 'P'){
    $status_condition = "$role_id_condition
            AND a.application_status NOT IN ('D','H','A','R')";
        }else{
            if($status=='F'){
                $status_condition = "AND a.where_app_is_role_id!=$role_id AND a.application_status='".$status."'";
            }else{
                $status_condition = "AND a.application_status='".$status."'";
            }
        }


    
            
       
    $applications = Yii::$app->db->createCommand("SELECT 
            a.id as app_id, 
            CONCAT('EA-NAPDDR-000',a.id) as project_id,
            a.scpm_id as scpm_id,
            a.sso_user_id as sso_user_id,
            u.name as applicant,
            d.dept_name as department, 
            s.service_name as service, 
            a.application_status as status, 
            CASE a.application_status
                WHEN 'D' THEN 'Draft'
                WHEN 'P' THEN 'Pending'
                WHEN 'H' THEN 'Reverted'
                WHEN 'F' THEN 'Pending'
                WHEN 'A' THEN 'Approved'
                WHEN 'R' THEN 'Rejected'
                ELSE 'Unknown'
            END AS status_label,
            a.created_on as action_on
            FROM t_application_submission a
            INNER JOIN service_config_parameter_mapping scpm ON a.scpm_id=scpm.id
            INNER JOIN mst_services s ON scpm.service_id = s.id
            INNER JOIN mst_departments d ON scpm.dept_id = d.id
            INNER JOIN users u ON a.sso_user_id = u.id
            WHERE a.is_active=1 $dept_id_condition
            $status_condition
            ORDER BY a.id DESC
            ")->queryAll();

        return ['counts'=>$count_array,'applications'=>$applications,'total_count'=>sizeof($applications)];


}

protected function departmentData($role_type,$role_id,$user_id=NULL,$status=NULL){

}

protected function boadmin($role_type,$role_id,$user_id=NULL,$status=NULL){

}

    

    public function actionServiceDashboard(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['service_id']) && Helper::CheckNotEmptyCondition($_POST['service_id']) && $_POST['service_id']>0){
                $scpm_query = ServiceConfigParameterMapping::find()->where(['is_workflow_done'=>1,'is_active'=>1,'service_id'=>$_POST['service_id']])->all(); 
            }else{
                $scpm_query = ServiceConfigParameterMapping::find()
    ->where(['is_workflow_done' => 1, 'is_active' => 1])
    ->andWhere(['not in', 'service_id', [90, 91, 92, 93, 94]])
    ->all();
            }
                
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


    

    public function actionGetAllServices(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        $service = Yii::$app->db->createCommand("SELECT s.id as id, s.service_name as service_name FROM service_config_parameter_mapping scpm
        INNER JOIN mst_services s ON scpm.service_id = s.id  ")->queryAll();
        return [
            'status'=>true,
            'message'=>'List Of All Services/Schemes',
            'services'=>$service,
            'token'=>$token
        ];
    }

    public function actionRoleType(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        $role_type = ['FO'=>'Applicant User','BO'=>'Back Office Users'];
        return [
            'status' => true,
            'role_type' => $role_type,
            'token' => $token
        ];
    } 

    public function actionRoleName(){
        $token = Token::tokenGenerator(Yii::$app->user->id);

        if(isset($_POST['role_type']) && Helper::CheckNotEmptyCondition($_POST['role_type'])){
            $role_type_condi = "AND r.role_type ='".$_POST['role_type']."' ";
        }else{
            $role_type_condi = "";
        }

        $userroles = Yii::$app->db->createCommand("SELECT r.id as id, r.role_name_label as role_name_label from mst_userrole r where r.role_type NOT IN ('DU','NA') $role_type_condi")->queryAll();
        return [
            'status' => true,
            'userroles' => $userroles,
            'token' => $token
        ];
    }

    public function actionDepartments(){
       $token = Token::tokenGenerator(Yii::$app->user->id);
       $departments = Yii::$app->db->createCommand("SELECT id, dept_name from mst_departments")->queryAll();
        return [
            'status' => true,
            'departments' => $departments,
            'token' => $token
        ];
    } 

    public function actionGender(){
       $token = Token::tokenGenerator(Yii::$app->user->id);
       $gender = OptionValue::GetOptionValue(5);
        return [
            'status' => true,
            'gender' => $gender,
            'token' => $token
        ];
    }

}
