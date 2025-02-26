<?php 
namespace app\controllers;
// this controller is for BO user
use app\models\Token;
use app\models\Helper;

use Yii;
use app\modules\application\controllers\RestController;
use yii\web\Response;
use agielks\yii2\jwt\JwtBearerAuth;
use yii\filters\Cors;
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
use app\models\ServiceBoprocessRoleEngine;
use app\models\masters\Document;
use app\models\masters\OptionValue;
use app\models\transactions\TApplicationSubmission;
use app\models\transactions\TApplicationLog;
use app\models\transactions\TApplicationDms;
use app\models\transactions\TDmsVerification;
use app\models\transactions\TApplicationBoLog;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use app\models\User;
use app\models\ServiceBoprocessFormFieldsMapping;
use app\models\ServiceBoprocessActionAcess;
use app\models\ServiceBoprocessActionPassTo;


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
        
class ServicemanageController extends RestController{

	/**
     * @inheritdoc
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

     

	public function actionIndex(){
		$command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['application_id'])){
                $model = TApplicationSubmission::findOne($_POST['application_id']);
                 if($model){
                    $main_detail = [
                        'service_name'=>$model->scpm->service->service_name,
                        'department_name'=>$model->scpm->dept->dept_name,
                        'application_id'=>$model->id,
                        'applicant_name'=>$model->ssouser->name,
                        'submitted_on'=>date('d M Y h:i a',strtotime($model->created_on)),
                        'status'=>$model->application_status
                    ];

                    // form fields code started here
                    $field_data = (array) json_decode($model->form_field_data,true);
                    $formfield_details = [];
                    foreach ($field_data as $tab_mapping_id => $value) {

                        $tabDetail = ServiceTabMapping::findOne($tab_mapping_id);

                        $sectionDetail = ServiceFormTabSectionMapping::find()->where(['stm_id'=>$tabDetail->id])->orderBy('preference_order ASC')->all();

                        $section_array = [];
                        foreach ($sectionDetail as $skey => $svalue) {
                            $field_data = ServiceFormTabSectionFormFieldsMapping::find()->where(['sftsm_id'=>$svalue->id])->orderBy('preference_order ASC')->all();

                            foreach ($field_data as $fkey => $fvalue) {
                                if(array_key_exists($fvalue->ff->form_field_id, $value)){
                                    $is_array= is_array($value[$fvalue->ff->form_field_id]) ? true : false;

             $dynamic_options_dt = FieldDatatype::GetOptionsfieldtype();
                                    

                                
            if($is_array==true && @$fvalue->fdt->type=='addmore'){
                // get table header
            $zero_index_for_heading = $value[$fvalue->ff->form_field_id][0];
                    $thead = [];
                    foreach ($zero_index_for_heading as $tk => $tv) {
                    $mst_ff = $command->createCommand("SELECT form_field_name FROM mst_form_fields WHERE form_field_id='".$tv['form_field_code_id']."'")->queryOne();
                        $thead[] = @$mst_ff['form_field_name'] ;
                    }
                                        
                    $field_value = [
                        'thead' => $thead,
                        'tbody'=>$value[$fvalue->ff->form_field_id],
                    ];

                }else{
                    if(in_array($fvalue->fdt->type, $dynamic_options_dt)){                
                        $value_label = OptionValue::find()->where(['id'=>$value[$fvalue->ff->form_field_id]])->one();
                        $values = $value_label->name;
                    }else{
                        $values = $value[$fvalue->ff->form_field_id];
                    }  
                    $field_value = $values;
                }

                    $section_array[$svalue->sc->section][] = [
                                        'field_name'=>$fvalue->field_name,
                                        'field_is_array_value' => $is_array,
                                        'field_type' => @$fvalue->fdt->type,
                                        'field_value'=>$field_value
                                    ];
                                }
                                 
                            }
                        }

                        $formfield_details[$tabDetail->tab_name] = $section_array;
                    }
                    // form fields code end here

                    //DMS code started here
                    $dms_detail = ServiceDmsMapping::getDMS_mapped_withUploaded_data($model->scpm_id,$model->id);

                    //DMS code end here

$roleEngine = ServiceBoprocessRoleEngine::find()->where(['scpm_id'=>$model->scpm_id,'role_id'=>Yii::$app->user->identity->role_id,'is_active'=>1])->one();  
if($roleEngine){
    //department data code
    $sql = "SELECT * FROM service_boprocess_action_acess         
        WHERE role_engine_id = :role_engine_id AND is_active=1";
    $command = Yii::$app->db->createCommand($sql);
    $command->bindValue(':role_engine_id', $roleEngine['id']);
    $access_actions = $command->queryAll();

    // department form fields mapped
    $field_data = ServiceBoprocessFormFieldsMapping::find()->where(['role_engine_id'=>$roleEngine['id']])->orderBy('preference_order ASC')->all();
    $fields_data=[];
   foreach ($field_data as $k => $val) {
    $fields_data[]=[
                    'bo_process_form_field_mapping_id' => $val->id,
                    'ff_id'=>$val->ff->form_field_id,
                    'field_name'=>$val->field_name,
                    'field_type'=>$val->fdt->type,
                    'is_required'=>$val->is_required,
                    'placeholder'=>$val->placeholder,               
                    'preference_order'=>$val->preference_order,                    
                ];
   }

}else{
    $fields_data=$access_actions=[];
}                   






                        return $this->asJson([
                            'status' => true,
                            'data'=>[
                                'main_detail'=>$main_detail,
                                'formfield_detail'=>$formfield_details,
                                'dms_detail' => $dms_detail
                            ],
                            'department_data' => [
                                'fields_data' => $fields_data,
                                'access_actions' => $access_actions,
                            ],
                            'token' => $token                   
                        ]);
                    }else{
                         return $this->asJson([
                            'status' => false, 'message'=>'Application not found',
                            'token' => $token,  
                        ]);
                    }                 
            }else{
                return $this->asJson([
                    'status' => false, 'message'=>'Parameter missing',
                    'token' => $token,  
                ]);
            }            
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
	}

    public function actionActionDepends(){
        $token = Token::tokenGenerator(Yii::$app->user->id);

        if(isset($_POST['id'])){
            $model = ServiceBoprocessActionAcess::find()->where(['id'=>$_POST['id']])->one();
            if($model){
                $passModel = ServiceBoprocessActionPassTo::find()->where(['is_active'=>1,'action_acess_id'=>$model->id])->all();
                $passREData = [];
                foreach ($passModel as $key => $value) {
                    $passREData[] = [
                        'pass_to_id'=>$value->id,
                        'action_acess_id' => $value->action_acess_id,
                        'passto_role_engine_id' => $value->passto_role_engine_id,
                        'role_id' => $value->role_id,
                        'role_name'=>$value->role->role_name,
                        'role_label_name'=>$value->role->role_name_label,
                        'selected_action' => $value->actionacess->action_access
                    ];
                }
                return $this->asJson([
                    'status' => true,
                    'passREData'=> $passREData,
                    'token' => $token                   
                ]);
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'No data found',
                    'token' => $token                   
                ]);
            }
            
        }else{
            return $this->asJson([
                    'status' => false, 
                    'message'=>'Parameter missing',
                    'token' => $token,  
                ]);
        }
    }

    public function actionGetDepartments(){
        $token = Token::tokenGenerator(Yii::$app->user->id);

      
        $departments = \app\models\masters\Departments::find()->all();
        
        return $this->asJson([
            'status' => true,
            'departments'=> $departments,
            'token' => $token                   
        ]);       
        
    }


    public function actionActionOnDms(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['application_dms_id']) && isset($_POST['action']) && $_POST['application_dms_id'] && $_POST['action']){
            
         $dms = TApplicationDms::find()->where(['id'=>$_POST['application_dms_id']])->one();  
         if($dms){
            $model = new TDmsVerification;
            $model->t_app_dms_id = $dms->id;
            $model->user_id = Yii::$app->user->id;
            $model->status = $_POST['action'];
            $model->comment = $_POST['remark'];
            $model->created_on = date('Y-m-d H:i:s');
            if(!$model->save()){
                return $model->errors;
            }else{
                $dms->dms_status = $model->status;
                $dms->updated_on = date('Y-m-d H:i:s');
                $dms->save();
            }

             //DMS code started here
              $dms_detail = ServiceDmsMapping::getDMS_mapped_withUploaded_data($dms->app->scpm_id,$dms->application_id);

            return $this->asJson([
                'status' => true, 
                'message'=>'action saved on this document',
                'dms_detail' => $dms_detail,
                'token' => $token,  
            ]);
         }else{
            return $this->asJson([
                'status' => false, 'message'=>'No Data Found',
                'token' => $token,  
            ]);
         }

        }else{
            return $this->asJson([
                'status' => false, 'message'=>'Parameter missing',
                'token' => $token,  
            ]);
        }
    }

    public function actionProcessdata(){

         $command = Yii::$app->db;
    $cdate = date('Y-m-d H:i:s');
    $application_forward_departments_id = null;

    if(Yii::$app->user->id){
        $token = Token::tokenGenerator(Yii::$app->user->id);

        if(isset($_POST['application_id']) && isset($_POST['action'])){
            $application_id = $_POST['application_id'];
            $action = $_POST['action'];
            $pass_to = $_POST['pass_to'];
            $department_ids = $_POST['department_ids'];
            $bo_comment = $_POST['bo_comment'];

$model = TApplicationSubmission::findOne($application_id);

if($model){

$getActionPerform = ServiceBoprocessActionAcess::GetActionDetail($action );

if($getActionPerform){
    

//departments userrole k liye 
    if(Yii::$app->user->identity->role_name=='department'){
        //first find the department data
        $appForwdDept = TApplicationForwardDepartments::find()->where(['application_id'=>$model->id,'department_id'=>Yii::$app->user->identity->dept_id])->one();
        if($appForwdDept){
            $appForwdDept->status = TApplicationSubmission::BouserActions_applicationStatus($getActionPerform['action_access']);
            $appForwdDept->updated_on = $cdate;
            $appForwdDept->updated_by = Yii::$app->user->id;
            $appForwdDept->save();
            $application_forward_departments_id = $appForwdDept->id;
            $log_status = $appForwdDept->status;
            $application_status = $model->application_status;
            $log_comment = "application was processed by ".Yii::$app->user->identity->dept_name."  department";
        }else{
            return [
                'status'=>false,
                'message'=>'something went wrong while department processing',
                'token'=>$token
            ];
        }

    }else{
        switch ($getActionPerform['action_access']) {
            case 'approve':
                $log_status = $application_status = 'A';
                $log_comment = 'Application has been Approved';
                $model->where_app_is_role_id = 0;
                break;
            case 'reject':
                $log_status = $application_status = 'R';
                $log_comment = 'Application has been Rejected';
                $model->where_app_is_role_id = 0;
                break;
            case 'forward':
                $responseData = $this->forwardapplication($model, $cdate, $pass_to, $department_ids);
                $log_status = $responseData['log_status'];
                $application_status = $responseData['app_status'];
                $log_comment = $responseData['comment'];
                $model->where_app_is_role_id = $responseData['now_where_app_is_role_id'];
                break;        
            case 'revert':
                $responseData = $this->revertapplication($model, $cdate, $pass_to);
                $log_status = $responseData['log_status'];
                $application_status = $responseData['app_status'];
                $log_comment = $responseData['comment'];
                $model->where_app_is_role_id = $responseData['now_where_app_is_role_id'];
                break;
            default:
                $log_status = $application_status = 'NA';
                $log_comment = 'Application Stuck due to invalid action taken by BO user '.$_POST['actions'];
                break;
        }

        // current bo user role engine  records
    
   

            $model->application_status = $application_status;
            $model->updated_on = date('Y-m-d H:i:s');
            $model->save();

            }    
        }else{
            return $this->asJson([
                    'status' => false, 'message'=>'Not a valid action',
                    'token' => $token,  
                ]);
        }
 

        $log = new TApplicationLog;
        $log->application_id = $model->id;
        $log->user_id = Yii::$app->user->id;
        $log->role_id = Yii::$app->user->identity->role_id;
        $log->application_status = $log_status; 
        $log->comment = $log_comment;
        $log->description = NULL;
        $log->created_on =  date('Y-m-d H:i:s');
        $log->save();

        $boLog = new TApplicationBoLog;
        $boLog->application_log_id = $log->id;
        $boLog->form_field_data = json_encode(['bo_comment'=>$bo_comment]);
        $boLog->action_taken = $log_status;
        $boLog->created_on =  date('Y-m-d H:i:s');
        $boLog->bo_user_id = Yii::$app->user->id;
        $boLog->bo_role_id = Yii::$app->user->identity->role_id;
        $boLog->application_forward_departments_id = $application_forward_departments_id;
        $file_detail = UploadedFile::getInstanceByName('supporting_doc');
                if($file_detail){
                    if(!is_dir('uploads')){
                       FileHelper::createDirectory('uploads');
                     }
                    if(!is_dir('uploads/bo_supporting/'.$model->id)){
                            FileHelper::createDirectory('uploads/bo_supporting/'.$model->id);
                    }

                    $directory = Yii::getAlias('uploads/bo_supporting/'.$model->id).'/';
                    $file_path = $directory.$file_detail->name;
                    if($file_detail->saveAs($file_path)){
                        $boLog->supporting_doc = $file_path;
                    }                        
                }
//bashini API call 
//$bhashini_response = Helper::BhashiniTTS('hi','female',$form_fields['bo_comment']);
// if($bhashini_response['bhashinistatus']==true){
//     $boLog->translation_text = $bhashini_response['data'];
//     $boLog->audio_file_path = $bhashini_response['audio_file_path'];
// }
$boLog->save(false);
// if(!$boLog->save()){
//     return $this->asJson([
//         'status' => false,
//         'message' => $boLog->errors,
//         'token' => $token                   
//     ]);
// }


                    return $this->asJson([
                        'status' => true,
                        'message' => $log_comment,
                        'token' => $token                   
                    ]);


                }else{
                     return $this->asJson([
                        'status' => false,
                        'message'=>'Application not found',
                        'token' => $token,  
                    ]);
                }                    
                        
            }else{
                return $this->asJson([
                    'status' => false, 'message'=>'Parameter missing',
                    'token' => $token,  
                ]);
            }            
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
       

       
    }

    protected function forwardapplication($model, $cdate, $pass_to, $department_ids){
    $getPassToDetail = null;
if($pass_to!=null || $pass_to!='null'){

    $getPassToDetail = ServiceBoprocessActionPassTo::GetPassToDetail($pass_to);
}

if($getPassToDetail!=null){
    if($getPassToDetail->role->role_name=='department'){
        $passDeptArray = explode(',', $department_ids);
        if(is_array($passDeptArray) && !empty($passDeptArray)){
            foreach($passDeptArray as $dept){
                $appForwdDept = new TApplicationForwardDepartments;
                $appForwdDept->application_id = $model->id;
                $appForwdDept->department_id = $dept;
                $appForwdDept->district_id = $model->district_id;
                $appForwdDept->state_id = $model->state_id;      
                $appForwdDept->role_id = $getPassToDetail->role_id;
                $appForwdDept->status = 'P';
                $appForwdDept->created_on = $cdate;
                $appForwdDept->created_by = Yii::$app->user->id;
                $appForwdDept->updated_on = $cdate;
                $appForwdDept->updated_by = Yii::$app->user->id;
                $appForwdDept->save();
            }
        }
        $app_status = $model->application_status;
        $log_status = 'F';
        $now_where_app_is_role_id = $model->where_app_is_role_id;
        $comment = 'Application was farwarded to line departments';
    }else{
        $log_status = $app_status = 'F';
        $now_where_app_is_role_id = $getPassToDetail->role_id;
        $comment = 'Application was farwarded to '.$getPassToDetail->role->role_name_label;
    }

    return [
        'app_status'=>$app_status,
        'log_status'=>$log_status,
        'now_where_app_is_role_id'=>$now_where_app_is_role_id,
        'comment'=>$comment
    ];
}
}

//this function call in same class processData action. this function not handle revert to department
protected function revertapplication($model, $cdate, $pass_to){
        $getPassToDetail = null;
    if($pass_to!=null || $pass_to!='null'){
        $getPassToDetail = ServiceBoprocessActionPassTo::GetPassToDetail($pass_to);
    }

    if($getPassToDetail!=null){
        if($getPassToDetail->role->role_type=='FO'){
            $app_status = 'H';
            $log_status = 'H';
            $now_where_app_is_role_id = $getPassToDetail->role_id;;
            $comment = 'Application was reverted to '.$getPassToDetail->role->role_name_label;
        }else{

            $checkIFFirstLevelBO  = ServiceBoprocessRoleEngine::find()
            ->alias('re')
            ->joinWith('role mur')
            ->where(['re.scpm_id'=>$model->scpm_id,'re.is_active'=>1])
            ->andWhere(['not in','mur.role_type',['FO']])
            ->orderBy('re.level_stage_no ASC')->one(); 

            if($checkIFFirstLevelBO->role_id==$getPassToDetail->role_id){
                $app_status = 'P';
                $log_status = 'P';
                $now_where_app_is_role_id = $getPassToDetail->role_id;
                $comment = 'Application was reverted to '.$getPassToDetail->role->role_name_label;
            }else{
                $app_status = 'F';
                $log_status = 'H';
                $now_where_app_is_role_id = $getPassToDetail->role_id;
                $comment = 'Application was reverted to '.$getPassToDetail->role->role_name_label;
            }
        }

        return [
            'app_status'=>$app_status,
            'log_status'=>$log_status,
            'now_where_app_is_role_id'=>$now_where_app_is_role_id,
            'comment'=>$comment
        ];
    }
}

    public function actionTimeline(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['application_id'])){
                $model = TApplicationSubmission::findOne($_POST['application_id']);
                 if($model){
                    $main_detail = ['service_name'=>$model->scpm->service->service_name,'department_name'=>$model->scpm->dept->dept_name,'application_id'=>$model->id,'applicant_name'=>$model->ssouser->name,'submitted_on'=>date('d M Y h:i a',strtotime($model->created_on)),'status'=>TApplicationSubmission::applicationStatus($model->application_status)['application_status_label']];
                    $timeline= TApplicationLog::find()->where(['application_id'=>$model->id])->orderBy('created_on ASC')->all();
                    $timeline_data = [];
                    $total_days = 0;
                    foreach ($timeline as $key => $value) {
                        if($key === 0){
                            $days = 0;
                        }

                        if ($key > 0) {
                            $startDate = $timeline[$key - 1]['created_on'];
                            $endDate = $value['created_on'];
                            $daysDifference = TApplicationLog::GetCalculateDays($startDate, $endDate);

                            $days = $daysDifference;
                        }
                        $total_days = $total_days+$days;
                        $timeline_data[] = [
                            'action_by'=>($value->user_id ? $value->user->name : 'NA'),
                            'comment'=>$value->comment,
                            'log_status'=>TApplicationSubmission::applicationStatus($value->application_status)['application_status_label'],
                            'action_on' => date('d M Y h:i a',strtotime($value->created_on)),
                            'days'=>$days
                        ];
                    }

                    $boLog = Yii::$app->db->createCommand("SELECT bl.*, u.name as bo_user_name, ur.role_name_label FROM t_application_bo_log bl
                         INNER JOIN users u ON bl.bo_user_id = u.id
                          INNER JOIN mst_userrole ur ON bl.bo_role_id = ur.id
                        INNER JOIN t_application_log l ON bl.application_log_id = l.id
                        INNER JOIN t_application_submission a ON l.application_id=a.id
                        WHERE a.id=".$model->id)->queryAll();

                    $boData =[];

                    foreach ($boLog as $key => $value) {
                        $bo_FF_data = (array)json_decode($value['form_field_data']);
                        $translation_text = '';
                        if($value['translation_text']){
                            $translation_araay = (array) json_decode($value['translation_text'],true);
                            if(isset($translation_araay['pipelineResponse'][0]['output'][0]['target'])){
                                $translation_text =  $translation_araay['pipelineResponse'][0]['output'][0]['target'];
                            }
                            
                        }
                        $boData[] =[
                            'bo_user_name'=>$value['bo_user_name'],
                            'role_name_label'=>$value['role_name_label'],
                            'comment'=>$bo_FF_data['bo_comment'],
                            'translation_text'=>$translation_text,
                            'audio_file_url'=>($value['audio_file_path'] ? (Url::base(true).'/'.$value['audio_file_path']) : ""),
                            'supporting_doc'=>(@$bo_FF_data['ff27'] ? $bo_FF_data['ff27'] : ""),
                            'action_taken'=>$value['action_taken'],
                            'created_on'=>date('d M Y h:i a',strtotime($value['created_on'])),
                        ];
                    }

                    return $this->asJson([
                            'status' => true,
                            'data'=>[
                                'main_detail'=>$main_detail,
                                'timeline_data'=>$timeline_data,
                                'total_days' => $total_days,
                                'boLog'=>$boData
                            ],
                            
                            'token' => $token                   
                        ]);
                     
                }else{
                     return $this->asJson([
                        'status' => false, 'message'=>'Application not found',
                        'token' => $token,  
                    ]);
                }                    
                        
            }else{
                return $this->asJson([
                    'status' => false, 'message'=>'Parameter missing',
                    'token' => $token,  
                ]);
            }            
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        }
    }   
    
}

?>