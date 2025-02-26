<?php 
namespace app\controllers;
// this is for FO user

use app\models\Token;
use app\models\Helper;

use Yii;
use yii\web\Response;
use agielks\yii2\jwt\JwtBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use app\models\ServiceConfigParameterMapping;
use app\models\ServiceTabMapping;
use app\models\ServiceFormTabSectionMapping;
use app\models\ServiceFormTabSectionFormFieldsMapping;
use app\models\ServiceFormFieldAddMoreMapping;
use app\models\ServiceBoprocessRoleEngine;
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
        
class ServiceworkflowController extends RestController{

    /**
     * @inheritdoc
     */
    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();
    //     $behaviors['authenticator'] = [
    //         'class' => JwtHttpBearerAuth::class,
    //      ];

    //     return $behaviors;
    // }
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
        //Yii::$app->session->remove('addmore_ff10');
        $rawjson = Yii::$app->request->getRawBody();
            if($rawjson){
                $_POST = json_decode($rawjson, true);
            }
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            
            if(isset($_POST['scpm_id'])){
                $model = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
                
                $current_tab_id = isset($_POST['tab_id']) ? $_POST['tab_id'] : 0;

                $nextTab = ServiceTabMapping::getnexttab($_POST['scpm_id'],$current_tab_id);
                    if($nextTab){
                        $activated_tab = ['tab_id'=>$nextTab->id,'short_code'=>$nextTab->tabType->short_code];
                        $serviceInfo = ['id'=>$model->id,'dept_name'=>$model->dept->dept_name,'service_name'=>$model->service->service_name,'is_workflow_done'=>$model->is_workflow_done];

                        return $this->asJson([
                            'status' => true,
                            'serviceInfo' => $serviceInfo,
                            'activated_tab'=>$activated_tab,
                            'token' => $token                   
                        ]);
                    }else{
                        if($current_tab_id==0){
                            return $this->asJson([
                                'status' => false, 'message'=>'This service or form is blank. There is no records',
                                'redirect_to'=>'dashboard',
                                'token' => $token,  
                            ]);
                        }else{
                            
                            return $this->asJson([
                                'status' => true, 'message'=>'Your application was submitted and forwarded to department',
                                'redirect_to'=>'dashboard',
                                'token' => $token,  
                            ]);
                        }
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


    public function actionGetInnerPagesDetails(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['tab_id'])){
                $model = ServiceTabMapping::findOne($_POST['tab_id']);
                $tab_array = $this->gettabandactivetab($model->scpm_id,$model->id);
                $application_data = TApplicationSubmission::find()
                         ->where(['scpm_id'=>$model->scpm_id,'is_active'=>1])
                         ->andWhere(['in','application_status',['D']])
                         ->orderBy('id DESC')->One();
                $application_id = $application_data ? $application_data->id : NULL ;        
                switch ($model->tabType->short_code) {
                    case 'FF':                        
                         
                         $tab_data = [];
                         if($application_id!=NULL){
                            $field_data = (array) json_decode($application_data->form_field_data,true);
                            if(isset($field_data[$model->id]) && is_array($field_data[$model->id]) && !empty($field_data[$model->id])){
                                $tab_data = $field_data[$model->id];
                            }
                         }
                        $data = $this->getformdata($model->id,$tab_data);
                        $tabdetail = ['data'=>$data,'tab_array'=>$tab_array,'application_id'=>$application_id];
                        break;

                    case 'DMS':
                        $dms_data = ServiceDmsMapping::getDMS_mapped_withUploaded_data($model->scpm_id,$application_id);
                        
                         $tabdetail = ['data'=>$dms_data,'tab_array'=>$tab_array,'application_id'=>$application_id];        
                        break;

                    case 'DEC':
                        $dec_data = ServiceDeclarationMapping::getDec_mapped_data($model->scpm_id);
                        $tabdetail = ['data'=>$dec_data,'tab_array'=>$tab_array,'application_id'=>$application_id];
                        break;

                    case 'MC':
                        $tabdetail = ['tab_array'=>$tab_array];
                        break;

                    case 'SIGN':
                        $tabdetail = ['tab_array'=>$tab_array];
                        break;

                    case 'PAY':
                        $tabdetail = ['tab_array'=>$tab_array];
                        break;

                  
                    default:
                        $tabdetail = ['tab_array'=>$tab_array];
                        break;
                }
                

                return $this->asJson([
                    'status' => true,
                    'model'=>$model,
                    'tabdetail'=>$tabdetail,
                    'token' => $token,                    
                ]);                
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing action for',
                    'token' => $token,      
                    
                ]);
            }            
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
    }

    

    public function actionDeleteAddMoreRow(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['add_more_btn_id_code'])){
                $add_more_btn_code = $_POST['add_more_btn_id_code'];
                $index = $_POST['row_id'];
                // below code to print data in view page
                unset($_SESSION['addmore_'.$add_more_btn_code][$index]);                
                $final_print_data = [];
                 foreach (Yii::$app->session->get('addmore_'.$add_more_btn_code) as $key => $value) {
                     $final_print_data[] = $value;
                 }
                 Yii::$app->session->set('addmore_'.$add_more_btn_code,$final_print_data);


                return $this->asJson([
                    'status' => true,
                    'data' => $final_print_data,
                    'add_more_btn_id_code' => $_POST['add_more_btn_id_code'],
                    'token' => $token              
                ]);
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


    public function actionSaveData(){
        $command = Yii::$app->db;
        
        if(Yii::$app->user->id){

            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(Yii::$app->user->identity->role_type!='FO'){
                return [
                    'status'=>false,
                    'message' =>'You cannot submit the applicant',
                    'token' => $token
                ];
            }

            if(isset($_POST['scpm_id']) && isset($_POST['tab_id']) && isset($_POST['form_fields_data'])){
                if($_POST['form_fields_data']){
                    $from_fields_data = json_decode($_POST['form_fields_data'], true);
                    if(is_array($from_fields_data) && !empty($from_fields_data)){
                        $config_param = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
                        $current_tab_mapping = ServiceTabMapping::findOne($_POST['tab_id']);

                        $model = new TApplicationSubmission;
                        $model->service_id = $config_param->service_id;
                        $model->scpm_id = $config_param->id;

                        $form_fields_final_data = [];
                        $session_keys = [];
                        foreach ($from_fields_data as $key => $value) {
                            $sql = "SELECT ffm.*, dt.type 
                            FROM service_form_tab_section_form_fields_mapping ffm
                            INNER JOIN mst_form_fields mff ON ffm.ff_id=mff.id 
                            INNER JOIN mst_field_datatype dt ON ffm.field_datatype_id=dt.id 
                            WHERE mff.form_field_id = :form_field_id";
                            $command = Yii::$app->db->createCommand($sql);
                            $command->bindValue(':form_field_id', $key);
                            $check_is_add_more_field = $command->queryOne(); 

                            if(!empty($check_is_add_more_field)){
                                if($check_is_add_more_field['is_add_more_field']==0){
                                    if($check_is_add_more_field['type']=='addmore'){
                                        if(Yii::$app->session->has('addmore_'.$key)){
                                        $form_fields_final_data[$key] = Yii::$app->session->get('addmore_'.$key);
                                        $session_keys[] = 'addmore_'.$key;
                                        }                                    
                                    }else{
                                        $form_fields_final_data[$key] = $value;
                                    }
                                } 
                            }else{
                                 return $this->asJson([
                                    'status' => false, 
                                    'data' => $from_fields_data,
                                    'message'=>'Form Field missing there is some internal configuration was changed',
                                    'token' => $token,  
                                ]);   
                            }
                        }

                        $model->form_field_data = json_encode([$_POST['tab_id']=>$form_fields_final_data]);
                        $model->application_status = 'D'; //Draft Stage
                        $model->sso_user_id = Yii::$app->user->id;
                        $model->created_on = date('Y-m-d H:i:s');
                        $model->updated_on = date('Y-m-d H:i:s');
                        $model->save();

                        $comment = $current_tab_mapping->tab_name.' was submitted.';
                        $this->updateapplicationstatus_insertlog($model->id,$comment,$_POST['tab_id']);

                        

                        // now check multipal data in session and remove from session
                        if(!empty($session_keys)){
                            foreach ($session_keys as $s_val) {
                                Yii::$app->session->remove($s_val);
                            }
                        }


                        return $this->asJson([
                            'status' => true,
                            'current_tab_id' => $current_tab_mapping->id,
                            'token' =>$token              
                        ]);

                        

                    }else{
                        return $this->asJson([
                            'status' => false, 
                            'data' => $from_fields_data,
                            'message'=>'Something went wrong while submitting form some fields missing',
                            'token' => $token,  
                        ]);
                    }
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

    public function actionSaveupdateData(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
             if(Yii::$app->user->identity->role_type!='FO'){
                return [
                    'status'=>false,
                    'message' =>'You cannot submit the applicant',
                    'token' => $token
                ];
            }
            if(isset($_POST['application_id']) && isset($_POST['form_fields_data'])){
                 
                 if($_POST['form_fields_data']){
                    $from_fields_data = json_decode($_POST['form_fields_data'], true);
                    if(is_array($from_fields_data) && !empty($from_fields_data)){
                        $config_param = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
                        $current_tab_mapping = ServiceTabMapping::findOne($_POST['tab_id']);

                        $model = TApplicationSubmission::findOne($_POST['application_id']);
                       
                        $form_fields_final_data = [];
                        $session_keys = [];
                        foreach ($from_fields_data as $key => $value) {

                            $sql = "SELECT ffm.*, dt.type 
                            FROM service_form_tab_section_form_fields_mapping ffm
                            INNER JOIN mst_form_fields mff ON ffm.ff_id=mff.id 
                            INNER JOIN mst_field_datatype dt ON ffm.field_datatype_id=dt.id 
                            WHERE mff.form_field_id = :form_field_id";
                            $command = Yii::$app->db->createCommand($sql);
                            $command->bindValue(':form_field_id', $key);
                            $check_is_add_more_field = $command->queryOne(); 

                            if(!empty($check_is_add_more_field)){
                                if($check_is_add_more_field['is_add_more_field']==0){
                                    if($check_is_add_more_field['type']=='addmore'){
                                        if(Yii::$app->session->has('addmore_'.$key)){
                                            $form_fields_final_data[$key] = Yii::$app->session->get('addmore_'.$key);
                                            $session_keys[] = 'addmore_'.$key;
                                        }                                    
                                    }else{
                                        $form_fields_final_data[$key] = $value;
                                    }
                                }     

                            }else{
                                 return $this->asJson([
                                    'status' => false, 
                                    'data' => $from_fields_data,
                                    'message'=>'Form Field missing there is some internal configuration was changed',
                                    'token' => $token,  
                                ]);   
                            }
                        }

                        $previuos_form_field_data = (array) json_decode($model->form_field_data,true);
                        

                        if(array_key_exists($_POST['tab_id'], $previuos_form_field_data)){
                           $previuos_form_field_data[$_POST['tab_id']] = $form_fields_final_data;
                           $final_form_fields_array = $previuos_form_field_data;
                        }else{
                           $current_tab_form_fields_array[$_POST['tab_id']] = $form_fields_final_data;
                           $final_form_fields_array = $previuos_form_field_data+$current_tab_form_fields_array;
                        }

                       

                        $model->form_field_data = json_encode($final_form_fields_array);
                        $model->application_status = 'D'; //Draft Stage
                        $model->sso_user_id = Yii::$app->user->id;
                        $model->updated_on = date('Y-m-d H:i:s');
                        $model->save();

                        $comment = $current_tab_mapping->tab_name.' was submitted.';
                        $this->updateapplicationstatus_insertlog($model->id,$comment,$_POST['tab_id']);

                        

// now check multipal data in session and remove from session
if(!empty($session_keys)){
    foreach ($session_keys as $s_val) {
        Yii::$app->session->remove($s_val);
    }
}


                        return $this->asJson([
                            'status' => true,
                            'current_tab_id' => $current_tab_mapping->id,
                            'token' => $token              
                        ]);

                        

                    }else{
                        return $this->asJson([
                            'status' => false, 
                            'data' => $from_fields_data,
                            'message'=>'Something went wrong while submitting form some fields missing',
                            'token' => $token,  
                        ]);
                    }
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

public function actionAddMoreSessionSave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['add_more_btn_id_code'])){
                $add_more_btn_code = $_POST['add_more_btn_id_code'];
                $print_data = [];
                //$original_data = [];
                if($_POST['new_data']!=''){
                    $data = json_decode($_POST['new_data']);                    
                    foreach($data as $key=>$val){
                        if($val->type =='select'){
                            $option_value_name = OptionValue::getValueByID($val->value);
                            $text = $option_value_name;
                        }else{
                            $text = $val->value;
                        }

                        $print_data[] = ['form_field_code_id'=>$val->form_field_code_id,'value'=>$val->value,'text'=>$text];   
                        //$original_data[] = $val->value;
                    }                    
                }    

                
                //below code is for data show in view page
                $final_print_data = [];
                if(Yii::$app->session->has('addmore_'.$add_more_btn_code)){
                    $previous_data = Yii::$app->session->get('addmore_'.$add_more_btn_code);
                    $final_print_data = array_merge($previous_data,[$print_data]);
                    Yii::$app->session->set('addmore_' . $add_more_btn_code, $final_print_data);
                    $final_print_data = $final_print_data;
                }else{                    
                    Yii::$app->session->set('addmore_' . $add_more_btn_code, [$print_data]);
                    $final_print_data = [$print_data];
                }

              
                return $this->asJson([
                    'status' => true,
                    'data' => $final_print_data,
                    'add_more_btn_id_code' => $_POST['add_more_btn_id_code'],
                    'token' => $token              
                ]);
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

    public function actionUploadDoc(){
        $command = Yii::$app->db;
       
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
             if(Yii::$app->user->identity->role_type!='FO'){
                return [
                    'status'=>false,
                    'message' =>'You cannot upload',
                    'token' => $token
                ];
            }
            if(isset($_POST['application_id']) && isset($_POST['dms_mapping_id'])){
               $application = TApplicationSubmission::findOne($_POST['application_id']);
               $model = TApplicationDms::find()->where(['application_id'=>$_POST['application_id'],'dms_mapping_id'=>$_POST['dms_mapping_id'],'dms_status'=>'P'])->One();     
               if(!$model){
                    $model = new TApplicationDms;
                    $model->application_id = $application->id;
                    $model->dms_mapping_id = $_POST['dms_mapping_id'];
                    $model->dms_status = 'P';
               }
                                        
                    $model->remark = $_POST['text'];
                    $file_detail = UploadedFile::getInstanceByName('file');
                    if($file_detail){
                        if(!is_dir('uploads')){
                           FileHelper::createDirectory('uploads');
                         }
                         if(!is_dir('uploads/dms/'.Yii::$app->user->id.'/'.$model->application_id)){
                                FileHelper::createDirectory('uploads/dms/'.Yii::$app->user->id.'/'.$model->application_id);
                            }

                            $directory = Yii::getAlias('uploads/dms/'.Yii::$app->user->id.'/'.$model->application_id).'/';
                            $file_path = $directory.$file_detail->name;
                            if($file_detail->saveAs($file_path)){
                                $model->file_url = $file_path;
                            }                        
                    }else{
                        return $this->asJson([
                            'status' => false,
                            'message' => 'file not found'               
                        ]); 
                    }

                    $model->created_on = date('Y-m-d H:i:s');
                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->save();

                    $dms_data = ServiceDmsMapping::getDMS_mapped_withUploaded_data($application->scpm_id,$application->id);

        
                return $this->asJson([
                    'status' => true,
                    'model' => $model,
                    'dms_data'=>$dms_data,
                    'token' => $token                   
                ]);
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

    public function actionSubmitDms(){
        $command = Yii::$app->db;
       
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
             if(Yii::$app->user->identity->role_type!='FO'){
                return [
                    'status'=>false,
                    'message' =>'You cannot submit',
                    'token' => $token
                ];
            }
            if(isset($_POST['application_id'])){

                $comment = 'Documents was uploaded.';
                $this->updateapplicationstatus_insertlog($_POST['application_id'],$comment,$_POST['tab_id']);
                            
                return $this->asJson([
                    'status' => true,
                    'current_tab_id' => $_POST['tab_id'],
                    'token' => $token                   
                ]);
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

    public function actionDecSubmit(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
             if(Yii::$app->user->identity->role_type!='FO'){
                return [
                    'status'=>false,
                    'message' =>'You cannot submit',
                    'token' => $token
                ];
            }
            if(isset($_POST['application_id'])){
                $comment = 'Declaration was submitted.';
                $this->updateapplicationstatus_insertlog($_POST['application_id'],$comment,$_POST['tab_id']);
                 
               return $this->asJson([
                    'status' => true,
                    'current_tab_id' => $_POST['tab_id'],
                    'token' => $token              
                ]);
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

    // this action is for dependent dropdown
    public function actionGetOptionsDependsOnParent(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['ff_parent_mapping_id']) && $_POST['ff_parent_mapping_id'] && $_POST['selectedValue']){


                $child_fields_depends_on_me = Yii::$app->db->createCommand("SELECT ffm.id, ff.form_field_id FROM service_form_tab_section_form_fields_mapping ffm
                    INNER JOIN mst_form_fields ff on ffm.ff_id=ff.id
                 WHERE ff.is_active=1 AND ffm.is_active=1 AND depends_on_sftsffm_id=".$_POST['ff_parent_mapping_id'])->queryAll();
                $data = [];
                if($child_fields_depends_on_me){
                    $chieldOptions = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$_POST['selectedValue']])->all();
                    $records = [];
                    foreach ($chieldOptions as $key => $value) {
                        $records[$value->id] = $value->name;
                    }  

                    foreach ($child_fields_depends_on_me as $key => $value) {
                        $data[] = [
                            'ff_mapping_id' => $value['id'],
                            'ff_id'=> $value['form_field_id'],
                            'options'=>$records
                        ];
                    }                  
                }
                 
               return $this->asJson([
                    'status' => true,
                    'data' => $data,
                    'token' => $token              
                ]);
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

/***************Protected function started here*************/    

    protected function gettabandactivetab($scpm_id,$tab_id){
        $tabs = ServiceTabMapping::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->orderBy('tab_type_id, preference_order ASC')->all();
                $tab_array = [];
               
                foreach($tabs as $k=>$v){
                    $active = "";
                    if($tab_id==$v->id){
                        $active = 'active';                        
                    }
                    $tab_array[] = [
                        'active_class'=>$active,
                        'original_preference_order'=>$v->preference_order,
                        'preference_order' => $k+1,
                        'tab_name'=>$v->tab_name,
                        'tab_id'=>$v->id,
                        'short_code'=>$v->tabType->short_code
                    ];
                }

           return $tab_array;     
    }

    protected function getformdata($tab_id,$tab_data){
        $data = [];
        $section_data = ServiceFormTabSectionMapping::find()->where(['stm_id'=>$tab_id,'is_active'=>1])->orderBy('preference_order ASC')->all();

        foreach ($section_data as $key => $value) {           

            $field_data = ServiceFormTabSectionFormFieldsMapping::find()->where(['sftsm_id'=>$value->id,'is_active'=>1])->orderBy('preference_order ASC')->all();

               $fields_data=[];
               foreach ($field_data as $k => $val) {
                // add more table data code
                $addmore_fields_column = $val->fdt->type =='addmore' ? $this->getaddmoremappingfields($val->id) : NULL;                
                
                // Dynamic/static option values code
                $options = NULL;
                if($val->option_master_id || $val->static_options){
                    $options = $this->getOptions($val);
                }

                // this line of code working on update mode to fetch data from application_submission table
                if($tab_data){
                    $default_data = NULL;
                    if($val->fdt->type =='addmore'){
                        $default_data = $tab_data[$val->ff->form_field_id];
                        Yii::$app->session->set('addmore_' . $val->ff->form_field_id, $default_data);
                    }else{
                        if($val->is_add_more_field==0){
                            $default_data = $tab_data[$val->ff->form_field_id];
                        }
                    }
                }else{
                    $default_data = $val->fdt->type =='addmore' ? [] : '';
                }

                /*$child_fields_depends_on_me = Yii::$app->db->createCommand("SELECT ffm.id, ff.form_field_id FROM service_form_tab_section_form_fields_mapping ffm
                    INNER JOIN mst_form_fields ff on ffm.ff_id=ff.id
                 WHERE ff.is_active=1 AND ffm.is_active=1 AND depends_on_sftsffm_id=".$val->id)->queryAll();*/

                 $child_fields_depends_on_me = Yii::$app->db->createCommand("
                    SELECT id From service_form_tab_section_form_fields_mapping                     WHERE is_active=1 AND depends_on_sftsffm_id=".$val->id)->queryOne();

                    $fields_data[]=[
                        'form_field_mapping_id' => $val->id,
                        'ff_id'=>$val->ff->form_field_id,
                        'field_name'=>$val->field_name,
                        'field_type'=>$val->fdt->type,
                        'is_required'=>$val->is_required,
                        'placeholder'=>$val->placeholder,
                        /*'option_master_id'=>$val->option_master_id,
                        'static_options'=>$statciOptionArray,*/
                        'options'=> $options,
                        'child_fields_depends_on_me' => ($val->option_master_id ? $child_fields_depends_on_me : NULL),
                        'depends_on_sftsffm_id'=>$val->depends_on_sftsffm_id,
                        'is_add_more_field'=>$val->is_add_more_field,
                        'preference_order'=>$val->preference_order,                    
                        'addmore_fields_column' => $addmore_fields_column,
                        'value'=>$default_data
                    ];
               }

               $data[] = ['section_id'=>$value->id,'section_name'=>$value->sc->section,'field_data'=>$fields_data];
        }


        return $data;
    }

    protected function getaddmoremappingfields($form_field_mapping_id){
        $sql = "SELECT am.id, am.form_field_id, ffm.field_name, ff.form_field_id as form_field_code_id, ft.type as field_type 
        FROM service_form_field_add_more_mapping am 
        INNER JOIN service_form_tab_section_form_fields_mapping ffm ON am.form_field_id = ffm.id
         INNER JOIN mst_form_fields ff ON ffm.ff_id = ff.id
         INNER JOIN mst_field_datatype ft ON ffm.field_datatype_id = ft.id
         WHERE am.is_active=1 AND am.add_more_field_id = :add_more_field_id ORDER BY ffm.preference_order ASC";
        $command = Yii::$app->db->createCommand($sql);
        $command->bindValue(':add_more_field_id', $form_field_mapping_id);
        $data = $command->queryAll();

        if($data){
            return $data;
        }else{
            return NULL;
        }
    }

    // protected function getaddmoremappingfields_values($form_field_mapping_id){
    //     return Yii::$app->session->get('addmore_ff10');
    // }

    protected function getOptions($val){        

        $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'option_id'=>$val->option_master_id])->all();
        $records = [];
        foreach ($data as $key => $value) {
            $records[$value->id] = $value->name;
        }

        if(empty($records)){
            if($val->static_options){
                $statciOptionArray = explode(',', $val->static_options);
                if(is_array($statciOptionArray)){
                    foreach ($statciOptionArray as $key => $value) {
                        $value = trim($value);
                        $records[$value] = $value;
                    }
                }
            }
        }

        return $records;
    }

    

    protected function updateapplicationstatus_insertlog($application_id,$comment,$current_tab_id){
        $application = TApplicationSubmission::findOne($application_id);
        $log = new TApplicationLog;
        $log->application_id = $application->id;
        $log->user_id = Yii::$app->user->id;
        $log->role_id = Yii::$app->user->identity->role_id;
        $log->created_on =  date('Y-m-d H:i:s');
        // check this last stage submitted then we will forwarded it to department

        $nextTab = ServiceTabMapping::getnexttab($application->scpm_id,$current_tab_id);
        $roleEngine = NULL;
        if(!$nextTab){
            $application->application_status = 'P';            
            
            $log->application_status = 'P'; //Pending means forwarded Stage
            $log->comment = $comment.' And application was forwarded to department level';
            $log->description = NULL;     

          
            $roleEngine  = ServiceBoprocessRoleEngine::find()
            ->alias('re')
            ->joinWith('role mur')
            ->where(['re.scpm_id'=>$application->scpm_id,'re.is_active'=>1])
            ->andWhere(['not in','mur.role_type',['FO']])
            ->orderBy('re.level_stage_no ASC')->one();

        }else{
            $log->application_status = 'D'; //Draft Stage
            $log->comment = $comment;
            $log->description = NULL;
        }

        $log->save();

        if($roleEngine!=NULL){
            $application->where_app_is_role_id = $roleEngine->role_id;
        }else{
            $fo_user_role = Yii::$app->db->createCommand("SELECT * FROM user_profile WHERE user_id=".$application->sso_user_id)->queryOne(); 
            $application->where_app_is_role_id = $fo_user_role['role_id'];                    
        }
        
        $application->updated_on = date('Y-m-d H:i:s');
        $application->save();
    }
}

?>