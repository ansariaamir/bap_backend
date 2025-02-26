<?php 
namespace app\controllers;
// this is a form builder controller specially use by developer account
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
use app\models\masters\SectionCategory;
use app\models\masters\FormFields;
use app\models\masters\FieldDatatype;
use app\models\ServiceDmsMapping;
use app\models\ServiceDeclarationMapping;
use app\models\masters\Document;
use app\models\masters\TabType;
use app\models\masters\Services;
use app\models\ServiceBoprocessRoleEngine;
use app\models\ServiceBoprocessActionAcess;
use app\models\ServiceBoprocessActionPassTo;
use app\models\ProjectConfigurations;
use app\models\ServiceOnboardLog;
use app\models\masters\OptionValue;
use app\models\masters\MstUserrole;
use app\models\ServiceBoprocessFormFieldsMapping;

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
        
class ConfigserviceController extends Controller{

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
        if(Yii::$app->user->id){
            $model = ServiceConfigParameterMapping::find()->all();
            $records = [];
            foreach ($model as $key => $value) {
                $records[] = ['id'=>$value->id,'dept_name'=>$value->dept->dept_name,'service_name'=>$value->service->service_name,'is_workflow_done'=>$value->is_workflow_done];
            }
            $token = Token::tokenGenerator(Yii::$app->user->id);
            return $this->asJson([
                'status' => true,
                'records' => $records,
                'token' => $token,      
                
            ]);
        }else{
             return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
	}

    


public function actionGetDetails(){
        if(Yii::$app->user->id){
            $project_config = ProjectConfigurations::getDetails();
            if($project_config['status']){

               
                if(isset($_POST['id'])){
                    $model = Yii::$app->db->createCommand("SELECT scpm.*, s.service_name as service_name,
                        d.dept_name as dept_name
                     FROM service_config_parameter_mapping scpm 
                        INNER JOIN mst_services s ON scpm.service_id = s.id
                        INNER JOIN mst_departments d ON scpm.dept_id = d.id
                        
                        WHERE scpm.id=:id")
                    ->bindValue(':id',$_POST['id'])
                    ->queryOne(); 
                   // $this->updateTabPreference($_POST['id']);
                    // ServiceConfigParameterMapping::findOne($_POST['id']);
                    if(isset($_POST['step'])){
                        $current_step = $_POST['step'];
                    }else{
                        $getLog = ServiceOnboardLog::find()->where(['scpm_id'=>$model['id']])->one();
                        $current_step = $getLog['next_step'];
                    }    
                   // $model = $model + ['service_name'=>$model->service->service_name];             
                }else{
                    $model = new ServiceConfigParameterMapping;    
                    $current_step = 1;                
                }

                $stepArray = $this->stepArray($project_config['data'],$current_step);

                $token = Token::tokenGenerator(Yii::$app->user->id);
                return $this->asJson([
                    'status' => true,
                    'stepArray' => $stepArray,
                    'project_config'=>$project_config['data'],
                    'model'=>$model,                   
                    'token' => $token,
                             
                ]);
            }else{
                return $this->asJson([
                    'status' => false, 
                    'message'=>'Project configuration was missing'
                ]);
            }            
        }else{
            return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
    }

    

    protected function stepArray($project_config,$current_step){ 
        $id = 1;  
        $step_array[] = ['id'=>$id,'name'=>'Onboard Service','code'=>'onboard_service'];

        if($project_config['dms_type_id']){
            $id = $id+1;
            $step_array[] = ['id'=>$id,'name'=>'DMS','code'=>'dms'];
        }

        // if($project_config['integrations_with_ids']){
        //     $id = $id+1;
        //     $step_array[] = ['id'=>$id,'name'=>'Exetrnal Integration'];
        // }

        // if($project_config['certificate_verification_ids']){
        //     $id = $id+1;
        //     $step_array[] = ['id'=>$id,'name'=>'Certificate'];
        // }

        if($project_config['bo_workflow']){
            $id = $id+1;
            $step_array[] = ['id'=>$id,'name'=>'Department Workflow','code'=>'workflow'];
        }

        

        $id = $id+1;
        $step_array[] = ['id'=>$id,'name'=>'Declaration','code'=>'declaration'];

        $id = $id+1;
        $step_array[] = ['id'=>$id,'name'=>'Mapped Form Fields','code'=>'formfield'];

        // $id = $id+1;
        // $step_array[] = ['id'=>$id,'name'=>'Signature Details','code'=>'SIGN'];

        $id = $id+1;
        $step_array[] = ['id'=>$id,'name'=>'Confirmation','code'=>'confirm'];


        
       
       $width = 100/sizeof($step_array);
       $step_array_f = [];  
        foreach ($step_array as $value) {
            
            if((int)$current_step==$value['id']){
                $bg = '';
            }else{
                 $bg = 'bg-secondary';
            }
            $step_array_f[]= [
                    'id'=> $value['id'],
                    'current_step'=>(int)$current_step,
                    'name'=> $value['name'], 
                    'width'=> $width,
                    'tab_code' => $value['code'],
                    'bg'=>$bg,
                    'total_step'=>sizeof($step_array)
                ];
        } 
        return  $step_array_f;
    }

    
    

     
    public function actionGetInnerPagesDetails(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['tab_code'])){
                $project_config = ProjectConfigurations::getDetails();
                    if($project_config['status']){
                        $eidArray = explode(',', $project_config['data']['entity_ids']);
                        if(isset($_POST['scpm_id']) && $_POST['scpm_id']!=null)
                            $model = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
                        else{
                            $model = NULL;              
                        }
                        switch ($_POST['tab_code']) {
                            case 'onboard_service':
                                
                                
                                $entity_ovArray = \app\models\masters\OptionValue::find()->where(['IN','id',$eidArray])->all(); 
                                $serviceArray = \app\models\masters\Services::getServicenotMapped();      

                                $tabdetail = ['entity_ovArray'=>$entity_ovArray,'serviceArray'=>$serviceArray];           
                                break;

                            case 'formfield':                        
                                $tabdetail = ServiceTabMapping::find()->where(['scpm_id'=>$model->id,'is_active'=>1,'tab_type_id'=>1])->all();           
                                break;

                            case 'dms':
                                $mapped_dms = ServiceDmsMapping::find()->where(['scpm_id'=>$model->id,'is_active'=>1])->all();
                                $ids = [];
                                foreach ($mapped_dms as $key => $value) {
                                    $ids[] = $value->doc_id;   
                                }

                                $ids = implode(',', $ids);
                                $not_in_cond ="";
                                if($ids){
                                    $not_in_cond = "AND id not in ($ids)";
                                }
                                $remain_dms = $command->createCommand("SELECT * FROM mst_document WHERE is_active=1 $not_in_cond")->queryAll();
                                $tabdetail = ['mapped_dms'=>$mapped_dms,'remain_dms'=>$remain_dms];          
                                break;

                            case 'declaration':
                                $mapped_dec = ServiceDeclarationMapping::find()->where(['scpm_id'=>$model->id,'is_active'=>1])->all(); 
                                $ids = [];
                                $mapped_dec_array = [];
                                foreach ($mapped_dec as $key => $value) {
                                    $ids[] = $value->declaration_id;   
                                    $mapped_dec_array[] = ['mapp_dec_id'=>$value->id,'dec_name'=>$value->declaration->declaration_name,'dec_des'=>$value->declaration->declaration_text,'preference_order'=>$value->preference_order];
                                }

                                $ids = implode(',', $ids);
                                $not_in_cond ="";
                                if($ids){
                                    $not_in_cond = "AND id not in ($ids)";
                                }
                                $remain_dec = $command->createCommand("SELECT * FROM mst_declarations WHERE deleted_at IS NULL $not_in_cond")->queryAll();

                                $tabdetail = ['mapped_dec'=>$mapped_dec_array,'remain_dec'=>$remain_dec];

                                break;

                            case 'mc':
                                $tabdetail = [];
                                break;

                            case 'signature_detail':
                                $tabdetail = [];
                                break;

                            case 'payment_service':
                                $tabdetail = [];
                                break;

                            case 'workflow':

                               
                                $tabdetail = [
                                    
                                    'bo_workflow_array' => Helper::GetStaticYesNo(),
                                    
                                    'roleEngineData'=>$this->roleenginedata($model->id),
                                ];
                                break;    

                            case 'workflow_done':
                                $tabdetail = [];
                                break;
                            
                            default:
                                $tabdetail = [];
                                break;
                        }
                        

                        return $this->asJson([
                            'status' => true,                            
                            'tabdetail'=>$tabdetail,
                            'project_config'=>$project_config['data'],
                            'model'=>$model,
                            'token' => $token,                    
                        ]); 
                    }else{
                        return $this->asJson([
                            'status' => false, 
                            'message'=>'Project configuration was missing'
                        ]);
                    }           
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

    public function actionGetdepartment(){
        if(Yii::$app->user->id){
            if(isset($_POST['entity_id']) && $_POST['entity_id'] && $_POST['entity_name']){
                if($_POST['entity_id']=='914'){
                    $type = 'M';
                }else{
                    $type = 'D';
                }
                $deptArray = \app\models\masters\Departments::getallData($type);
                $token = Token::tokenGenerator(Yii::$app->user->id);
                return $this->asJson([
                    'status' => true,
                   'deptArray' => $deptArray, 
                    'token' => $token,
                           
                ]);
            }else{
                return $this->asJson([
                'status' => false, 'message'=>'Parameter missing'
            ]);
            }
                     
        }else{
            return $this->asJson([
                'status' => false, 'message'=>'Session expired. Please login again'
            ]);
        } 
    }

    /*Initially service onboard create new entry*/
    public function actionCreate(){

        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['dept_id'])){

                $transaction = Yii::$app->db->beginTransaction();
    try {
        $model = new ServiceConfigParameterMapping;
        $model->created_on = date('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->id;

        $serviceModel = new Services;
        $serviceModel->service_name = $_POST['service_name'];
        $serviceModel->service_desc = $_POST['service_desc'];
        $serviceModel->created_at = date('Y-m-d H:i:s');
        $serviceModel->created_by = Yii::$app->user->id;
        $serviceModel->updated_at = date('Y-m-d H:i:s');
        $serviceModel->updated_by = Yii::$app->user->id;
        
        if (!$serviceModel->save()) {
            throw new Exception('Failed to save service model: ' . json_encode($serviceModel->errors));
        }

        $model->service_id = $serviceModel->id;
        $model->updated_by = Yii::$app->user->id;
        $model->updated_on = date('Y-m-d H:i:s');
        $model->dept_id = $_POST['dept_id'];
        $model->entity_id = $_POST['entity_id'];
        $model->country_id = $_POST['country_id'];

        if (isset($_POST['state_id']) && Helper::CheckNotEmptyCondition($_POST['state_id'])) {
            $model->state_id = $_POST['state_id'];
        } else {
            $model->state_id = null;
        }
        // $model->is_payment_service = $_POST['is_payment_service'];
        // $model->is_dms = $_POST['is_dms'];
        // $model->is_declaration = $_POST['is_declaration'];
        // $model->is_mc = $_POST['is_mc'];
        // $model->is_signature_detail = $_POST['is_signature_detail'];
        //$model->is_workflow_done = $_POST['is_workflow_done'];
        //$model->remark = $_POST['remark'];       

        $project_config = ProjectConfigurations::getDetails();
        $model->bo_workflow = @$project_config['data']['bo_workflow'];

        if (!$model->save(false)) {
            throw new Exception('Failed to save service config parameter mapping: ' . json_encode($model->errors));
        }

        $reModel = new ServiceBoprocessRoleEngine;
        $reModel->scpm_id = $model->id;
        $reModel->role_id = 2; // this is for FO role
        $reModel->level_stage_no = 0;
        $reModel->created_on = date('Y-m-d H:i:s');
        $reModel->created_by = Yii::$app->user->id;
        $reModel->updated_on = date('Y-m-d H:i:s');
        $reModel->updated_by = Yii::$app->user->id;
        $reModel->maxday_fpa = 0;

        if (!$reModel->save(false)) {
            throw new Exception('Failed to save service Boprocess role engine: ' . json_encode($reModel->errors));
        }

        $this->serviceconfiglog($model->id, $_POST['tab_code'], $_POST['tab_name'], $_POST['current_step']);

        // Commit the transaction
        $transaction->commit();

        return $this->asJson([
            'status' => true,
            'scpm_id' => $model->id,
            'message' => 'Service onboard done now setup the process',
            'token' => $token,
        ]);

    } catch (Exception $e) {
        // Rollback the transaction
        $transaction->rollBack();

        return $this->asJson([
            'status' => false,
            'message' => $e->getMessage(),
            'token' => $token,
        ]);
    }
                                    
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

    protected function serviceconfiglog($scpm_id,$tab_code,$tab_name,$current_step){
        $log = new ServiceOnboardLog;
        $log->scpm_id = $scpm_id;
        $log->tab_code = $tab_code;
        $log->tab_name = $tab_name;
        $log->current_step = $current_step;
        $log->next_step = $current_step+1;
        $log->created_on = date('Y-m-d H:i:s');
        $log->created_by = Yii::$app->user->id;
        $log->save();
        return true;
    }

// this action is for Tab add and mapped
    public function actionFormfieldssave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id']) && isset($_POST['tab_name'])){
                
                $sftm = new ServiceTabMapping;
                $sftm->scpm_id = $_POST['scpm_id'];
                $sftm->tab_type_id = 1; 
                $sftm->tab_name = $_POST['tab_name'];
                $sftm->preference_order = ServiceTabMapping::getnextPreferenceorderno($sftm->scpm_id);
                $sftm->created_on = date('Y-m-d H:i:s');
                $sftm->created_by = Yii::$app->user->id;
                if($sftm->save()){
                    $this->updateTabPreference($sftm->scpm_id);
                    return $this->asJson([
                        'status' => true,
                        'message' => 'Form Tab Mapping done',
                        'scpm_id'=>$sftm->scpm_id,
                        'stm'=>ServiceTabMapping::find()->where(['scpm_id'=>$sftm->scpm_id,'is_active'=>1,'tab_type_id'=>1])->all(),
                        'token' => $token,                    
                    ]); 
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($sftm->errors),
                        'token' => $token,                    
                    ]);
                }
                               
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

    public function actionGetformtabdetailssectionarray(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['ft_id'])){
               
                
                return $this->asJson([
                    'status' => true,
                    'message' => 'Form Tab Mapping done',
                    'allSectionArray' => SectionCategory::find(['is_active'=>1])->all(),
                    'stm'=>ServiceTabMapping::findOne($_POST['ft_id']),
                    'sftsm' => ServiceFormTabSectionMapping::getallSectionOnForm($_POST['ft_id']),
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

    public function actionSectionmappingsave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['stm_id'])){              
                $model = new ServiceFormTabSectionMapping;
                $model->stm_id = $_POST['stm_id'];
                $model->preference_order  = ServiceFormTabSectionMapping::getnextPreferenceorderno($_POST['stm_id']);
                if($_POST['section_id'] && $_POST['section_id']!="undefined"){
                    $model->sc_id = $_POST['section_id'];
                }else{
                    if($_POST['section_name']){
                        $sc = New SectionCategory;
                        $sc->section = $_POST['section_name'];
                        $sc->created_on = date('Y-m-d H:i:s');
                        $sc->created_by = Yii::$app->user->id;   
                        $sc->save();
                        $model->sc_id = $sc->id;                     
                    }else{
                        return $this->asJson([
                            'status' => false,
                            'message' => 'Please select section or add new section name',                    
                            'token' => $token,                    
                        ]);
                    }                    
                }

                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                    
                if($model->save()){
                    return $this->asJson([
                        'status' => true,
                        'message' => 'Section Mapping done',
                        'allSectionArray' => SectionCategory::find(['is_active'=>1])->all(),
                        'stm'=>ServiceTabMapping::findOne($_POST['stm_id']),
                        'sftsm' => ServiceFormTabSectionMapping::getallSectionOnForm($_POST['stm_id']),
                        'token' => $token,                    
                    ]);
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]); 
                }    

                                
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

    public function actionGetsectiondetailsformfieldarray(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['s_id'])){
               
               $selected_form_detail = ServiceFormTabSectionMapping::findOne($_POST['s_id']);
               $scpm_id = $selected_form_detail->stm->scpm_id;
                     $mapped_formfields_onservice = $command->createCommand("SELECT ffm.* FROM service_form_tab_section_form_fields_mapping ffm 
                        INNER JOIN service_form_tab_section_mapping sm ON ffm.sftsm_id=sm.id
                        INNER JOIN service_tab_mapping ftm ON sm.stm_id=ftm.id
                        WHERE ftm.scpm_id = $scpm_id
                        ")->queryAll();
                     $ids = [];
                        foreach ($mapped_formfields_onservice as $key => $value) {
                            $ids[] = $value['ff_id'];   
                        }

                        $ids = implode(',', $ids);
                        $not_in_cond ="";
                        if($ids){
                            $not_in_cond = "AND id not in ($ids)";
                        }
                        $remain_formfields = $command->createCommand("SELECT * FROM mst_form_fields WHERE is_active=1 $not_in_cond")->queryAll();

                        //get parent dynamic options sftsffm_ids for dependent child dropdown

               
                        
                return $this->asJson([
                    'status' => true,
                    'message' => 'Mapped Form Fields',
                    'allFormFieldArray' => $remain_formfields,
                    'alldataType' => FieldDatatype::find()->all(),
                    'sftsm'=>['id'=>$selected_form_detail->id,'section_name'=>$selected_form_detail->sc->section],
                    'sftsffm' => ServiceFormTabSectionFormFieldsMapping::getallFormFieldsOnSection($_POST['s_id']),
                    'options_masters' => \app\models\masters\Option::find()->where(['is_active'=>1])->all(),
                    'parent_fields_dynamic_options'=>ServiceFormTabSectionFormFieldsMapping::parent_fields_dynamic_options($_POST['s_id']),
                    'add_more_btns' => ServiceFormTabSectionFormFieldsMapping::getalladdmorebtnsOnSection($_POST['s_id']),
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

    public function actionFormfieldmappingsave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['sftsm_id']) && $_POST['field_datatype_id'] && $_POST['field_datatype_id']!='undefined' && $_POST['field_name']){              
                $model = new ServiceFormTabSectionFormFieldsMapping;
                $model->sftsm_id = $_POST['sftsm_id'];
                $model->field_datatype_id = $_POST['field_datatype_id'];
                $model->preference_order  = ServiceFormTabSectionFormFieldsMapping::getnextPreferenceorderno($_POST['sftsm_id']);
                if($_POST['ff_id']=='undefined' || $_POST['ff_id']==null || $_POST['ff_id']=='null'){
                    
                    $ff = New FormFields;
                    $ff->form_field_id = FormFields::getformfieldid();
                    $ff->form_field_name = $_POST['field_name'];
                    $ff->created_on = date('Y-m-d H:i:s');
                    $ff->created_by = Yii::$app->user->id;   
                    if(!$ff->save()){
                        return $this->asJson([
                            'status' => false,
                            'message' => json_encode($ff->errors),
                            'token' => $token,                    
                        ]); 
                    }
                    $model->ff_id = $ff->id;
                }else{
                     $model->ff_id = $_POST['ff_id'];                    
                                      
                }
                $model->field_name = $_POST['field_name'];

                if(Helper::CheckNotEmptyCondition($_POST['is_required'])){
                    $model->is_required = 1;
                }else{
                    $model->is_required = 0;
                }

                if($_POST['placeholder']=='undefined' || $_POST['placeholder']=='null'){
                    $model->placeholder = $model->field_name;
                }else{
                    $model->placeholder = $_POST['placeholder'];
                }

                if($_POST['option_master_id']=='undefined' || $_POST['option_master_id']=='null'){
                    $model->option_master_id = null;
                }else{
                    $model->option_master_id = $_POST['option_master_id'];
                }

                if($_POST['depends_on_sftsffm_id']=='undefined' || $_POST['depends_on_sftsffm_id']=='null'){
                    $model->depends_on_sftsffm_id = null;
                }else{
                    $model->depends_on_sftsffm_id = $_POST['depends_on_sftsffm_id'];
                }


                //$model->static_options = $_POST['static_options']=='undefined' ? NULL : $_POST['static_options'];

               
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                    
                if($model->save()){
                     $selected_section_detail = ServiceFormTabSectionMapping::findOne($_POST['sftsm_id']);
                     $scpm_id = $model->sftsm->stm->scpm_id;
                     $mapped_formfields_onservice = $command->createCommand("SELECT ffm.* FROM service_form_tab_section_form_fields_mapping ffm 
                        INNER JOIN service_form_tab_section_mapping sm ON ffm.sftsm_id=sm.id
                        INNER JOIN service_tab_mapping ftm ON sm.stm_id=ftm.id
                        WHERE ftm.scpm_id = $scpm_id
                        ")->queryAll();
                     $ids = [];
                        foreach ($mapped_formfields_onservice as $key => $value) {
                            $ids[] = $value['ff_id'];   
                        }

                        $ids = implode(',', $ids);
                        $not_in_cond ="";
                        if($ids){
                            $not_in_cond = "AND id not in ($ids)";
                        }
                        $remain_formfields = $command->createCommand("SELECT * FROM mst_form_fields WHERE is_active=1 $not_in_cond")->queryAll();

                    return $this->asJson([
                        'status' => true,
                        'message' => 'Form Field Mapping done',
                        'allFormFieldArray' => $remain_formfields,
                        'alldataType' => FieldDatatype::find()->all(),
                        'sftsm'=>['id'=>$selected_section_detail->id,'section_name'=>$selected_section_detail->sc->section],
                        'sftsffm' => ServiceFormTabSectionFormFieldsMapping::getallFormFieldsOnSection($_POST['sftsm_id']),
                        'options_masters' => \app\models\masters\Option::find()->where(['is_active'=>1])->all(),
                        'parent_fields_dynamic_options'=>ServiceFormTabSectionFormFieldsMapping::parent_fields_dynamic_options($selected_section_detail->id),
                        'add_more_btns' => ServiceFormTabSectionFormFieldsMapping::getalladdmorebtnsOnSection($selected_section_detail->id),
                        'token' => $token,            
                                 
                    ]);
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'ff_id' => $model->ff_id,
                        'token' => $token,                    
                    ]); 
                }    

                                
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

    public function actionUpdatePreferenceOrder(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['id']) && $_POST['id'] && isset($_POST['fromby']) && $_POST['fromby']){
            $id = $_POST['id'];
            switch ($_POST['fromby']) {
                case 'tab':
                    $model = ServiceTabMapping::find()->where(['id'=>$id])->one();
                    if($model){
                        $model->preference_order = $_POST['value'];
                        $model->updated_by = Yii::$app->user->id;
                        $model->updated_on = date('Y-m-d H:i:s');
                        $model->save();
                    }
                    break;
                
                case 'section':
                    $model = ServiceFormTabSectionMapping::find()->where(['id'=>$id])->one();
                    if($model){
                        $model->preference_order = $_POST['value'];
                        $model->updated_by = Yii::$app->user->id;
                        $model->updated_on = date('Y-m-d H:i:s');
                        $model->save();
                    }
                    break;

                case 'fields':
                    $model = ServiceFormTabSectionFormFieldsMapping::find()->where(['id'=>$id])->one();
                    if($model){
                        $model->preference_order = $_POST['value'];
                        $model->updated_by = Yii::$app->user->id;
                        $model->updated_on = date('Y-m-d H:i:s');
                        $model->save();
                    }
                    break;  
            }
            return $this->asJson([
                        'status' => true,
                        'message' => 'Preference Order Update',
                        'token' => $token,                    
                    ]);
        }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing action for',
                    'token' => $token,      
                    
                ]);
            }
    }

    

     public function actionMappedaddmorefieldssave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['btn_id']) && $_POST['formfield_id']){ 
                $sftsffm = ServiceFormTabSectionFormFieldsMapping::find()->where(['id'=>$_POST['formfield_id']])->one();
                $sftsffm->is_add_more_field = 1;
                $sftsffm->save(false);
                $model = new ServiceFormFieldAddMoreMapping;             
                $model->add_more_field_id = $_POST['btn_id'];
                $model->form_field_id = $_POST['formfield_id'];
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                    
                if($model->save()){
                    return $this->asJson([
                        'status' => true,
                        'message' => 'Form Field Mapped Into Add More',
                        'sftsffm' => ServiceFormTabSectionFormFieldsMapping::getallFormFieldsOnSection($sftsffm->sftsm_id),
                        'token' => $token,                    
                    ]);
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]); 
                }    

                                
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
    public function actionRemovemappaddmorefield(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if($_POST['formfield_id']){ 
                $model = ServiceFormFieldAddMoreMapping::find()->where(['is_active'=>1,'form_field_id'=>$_POST['formfield_id']])->One();
                if($model){
                    $sftsffm = ServiceFormTabSectionFormFieldsMapping::find()->where(['id'=>$_POST['formfield_id']])->one();
                    $sftsffm->is_add_more_field = 0;
                    $sftsffm->save(false);

                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = Yii::$app->user->id;   
                    $model->is_active = 0;      
                    if($model->save()){
                        return $this->asJson([
                            'status' => true,
                            'message' => 'Remove form field mapping',
                            'sftsffm' => ServiceFormTabSectionFormFieldsMapping::getallFormFieldsOnSection($sftsffm->sftsm_id),
                            'token' => $token,                    
                        ]);
                    }else{
                        return $this->asJson([
                            'status' => false,
                            'message' => json_encode($model->errors),
                            'token' => $token,                    
                        ]); 
                    }    
                }else{
                    return $this->asJson([
                            'status' => false,
                            'message' => 'No Mapping found',
                            'token' => $token,                    
                        ]); 
                }                          
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

/* Document Management System configuration action DMS*/
    public function actionDmssave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id']) && $_POST['doc_name'] && $_POST['allow_file_type'] && $_POST['allow_file_size']){              
                $model = new ServiceDmsMapping;
                $model->scpm_id = $_POST['scpm_id'];
                $model->preference_order  = ServiceDmsMapping::getnextPreferenceorderno($_POST['scpm_id']);

                if($_POST['doc_id'] && $_POST['doc_id']!="undefined"){
                    $model->doc_id = $_POST['doc_id'];
                }else{
                    $doc = New Document;                   
                    $doc->doc_name = $_POST['doc_name'];
                    $doc->created_on = date('Y-m-d H:i:s');
                    $doc->created_by = Yii::$app->user->id;   
                    $doc->save();
                    $model->doc_id = $doc->id;                                 
                }

                $model->doc_name = $_POST['doc_name'];
                $model->is_required = $_POST['is_required']=='undefined' ? 0 : 1 ;
                
                $model->allow_file_size = $_POST['allow_file_size']=='undefined' ? NULL : $_POST['allow_file_size'];
                $model->allow_file_type = $_POST['allow_file_type']=='undefined' ? NULL : $_POST['allow_file_type'];
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                    
                if($model->save()){

                    // all function to check and add DMS tab
                    $this->tabmappingcheckadd($_POST['scpm_id'],'DMS');

                     $mapped_dms = ServiceDmsMapping::find()->where(['scpm_id'=>$model->scpm_id,'is_active'=>1])->all();
                        $ids = [];
                        foreach ($mapped_dms as $key => $value) {
                            $ids[] = $value->doc_id;   
                        }

                        $ids = implode(',', $ids);
                        $not_in_cond ="";
                        if($ids){
                            $not_in_cond = "AND id not in ($ids)";
                        }
                        $remain_dms = $command->createCommand("SELECT * FROM mst_document WHERE is_active=1 $not_in_cond")->queryAll();
                         

                    $this->serviceconfiglog($model->scpm_id,$_POST['tab_code'],$_POST['tab_name'],$_POST['current_step']);

                    return $this->asJson([
                        'status' => true,
                        'message' => 'DMS Mapping Done',
                        'scpm_id' => $model->scpm_id,
                        'mapped_dms'=>$mapped_dms,
                        'remain_dms'=>$remain_dms,
                        'token' => $token,                    
                    ]);
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]); 
                }    

                                
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

/* Declaration Mapping Configuration action Save*/
    public function actionDecsave(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id'])  && $_POST['dec_id']){              
                $model = new ServiceDeclarationMapping;
                $model->scpm_id = $_POST['scpm_id'];
                $model->preference_order  = ServiceDeclarationMapping::getnextPreferenceorderno($_POST['scpm_id']);                
                $model->declaration_id = $_POST['dec_id'];
                
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                    
                if($model->save()){
                    // all function to check and add declaration tab
                    $this->tabmappingcheckadd($_POST['scpm_id'],'DEC');
                     $mapped_dec = ServiceDeclarationMapping::find()->where(['scpm_id'=>$_POST['scpm_id'],'is_active'=>1])->all(); 
                                $ids = [];
                                $mapped_dec_array = [];
                                foreach ($mapped_dec as $key => $value) {
                                    $ids[] = $value->declaration_id;   
                                    $mapped_dec_array[] = ['mapp_dec_id'=>$value->id,'dec_name'=>$value->declaration->declaration_name,'dec_des'=>$value->declaration->declaration_text,'preference_order'=>$value->preference_order];
                                }

                                $ids = implode(',', $ids);
                                $not_in_cond ="";
                                if($ids){
                                    $not_in_cond = "AND id not in ($ids)";
                                }
                                $remain_dec = $command->createCommand("SELECT * FROM mst_declarations WHERE deleted_at IS NULL $not_in_cond")->queryAll();

                                

                      // $this->serviceconfiglog($model->scpm_id,$_POST['tab_code'],$_POST['tab_name'],$_POST['current_step']);
                         

                    return $this->asJson([
                        'status' => true,
                        'message' => 'Declaration Mapping Done',
                        'scpm_id' => $model->scpm_id,
                        'mapped_dec'=>$mapped_dec_array,
                        'remain_dec'=>$remain_dec,
                        'token' => $token,                    
                    ]);
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]); 
                }    

                                
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

/*
*  this tab code not for form fields mapping 
*/
    protected function tabmappingcheckadd($scpm_id,$short_code){
        $tabType = TabType::find()->where(['short_code'=>$short_code])->one();
        if($tabType){
            $find_sftm = ServiceTabMapping::find()->where(['scpm_id'=>$scpm_id,'tab_type_id'=>$tabType->id,'is_active'=>1])->one();
            if(!$find_sftm){
                $sftm = new ServiceTabMapping;
                $sftm->scpm_id = $scpm_id;
                $sftm->tab_type_id = $tabType->id; 
                $sftm->tab_name = $tabType->tab_name;
                $sftm->preference_order = ServiceTabMapping::getnextPreferenceorderno($scpm_id);
                $sftm->created_on = date('Y-m-d H:i:s');
                $sftm->created_by = Yii::$app->user->id;
                $sftm->save();

                $this->updateTabPreference($scpm_id);
            }  
            return true;       
        }else{
            return false;
        }
    }


    /*Department workflow method and functions*/
    public function actionMappedRoleEngine(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id']) && isset($_POST['mapped_data'])){

$scpmModel = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
              
                // $mapped_data_array = $_POST['mapped_data'];
                $mapped_data_array = json_decode($_POST['mapped_data'], true);
               

              $sql = "UPDATE service_boprocess_role_engine re
INNER JOIN mst_userrole mur ON re.role_id = mur.id
SET re.is_active = 0
WHERE re.scpm_id = :scpm_id AND re.is_active = 1 AND mur.role_type NOT IN ('FO')";

              

                Yii::$app->db->createCommand($sql)
                    ->bindValue(':scpm_id', $scpmModel->id)
                    ->execute();

                foreach ($mapped_data_array as $key => $value) {
                    
                    $model = ServiceBoprocessRoleEngine::find()->where(['scpm_id'=>$scpmModel->id,'role_id'=>$value['role_id']])->one();
                    if(!$model){
                        $model = new ServiceBoprocessRoleEngine;
                        $model->scpm_id = $_POST['scpm_id'];
                        $model->role_id = $value['role_id'];
                        $model->created_on = date('Y-m-d H:i:s');
                        $model->created_by = Yii::$app->user->id;
                    }    

                    $model->level_stage_no = $key+1;               
                    $model->updated_by = Yii::$app->user->id;
                    $model->updated_on = date('Y-m-d H:i:s');



//     if($key==0){
//         $model->previous_role_id = 2; // 2 is for applicant mst_userrole table PK id
//     }else{
//         $model->previous_role_id = $mapped_data_array[$key-1]['role_id'];
//     }

//     if(($key+1)==sizeof($mapped_data_array)){
//         $model->next_role_id = 7; // 7 is for NA Type means end, no role mst_userrole table PK id
//     }else{
//         $model->next_role_id = $mapped_data_array[$key+1]['role_id'];
//     }

                    
                    
                    $model->is_active = 1;
                    $model->maxday_fpa = $model->maxday_fpa ? $model->maxday_fpa : Yii::$app->params['max_day_app_hold'];
                    $model->save();
                }
                                          
                return $this->asJson([
                        'status' => true,
                        'roleEngineData'=>$this->roleenginedata($_POST['scpm_id']),
                        'message' => 'Bo User Role Engine Mapping Done',
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


/*
*  Bo role engine workflow only update days for processing app
*/
    public function actionUpdateroleengine(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['re_id'])){
                $model = ServiceBoprocessRoleEngine::findOne($_POST['re_id']);
                $model->maxday_fpa = $_POST['app_days_to_role'];
                $model->updated_by = Yii::$app->user->id;
                $model->updated_on = date('Y-m-d H:i:s');          
                                    
                if($model->save()){

$roleEngine = $this->roleenginedata_withactionacess($model->id);
               
                        

                    return $this->asJson([
                        'status' => true,
                        //'roleEngineData'=>$this->roleenginedata($model->scpm_id),
                        'data' => $roleEngine,
                        'message' => 'Bo User Role Engine Mapping Updated',
                        'token' => $token,                    
                    ]);
                }else{
                     return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]);   
                }                   
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

    public function actionAddRoleActionAccess(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['re_id']) && isset($_POST['action_access']) && $_POST['re_id'] && $_POST['action_access']){

                $model = new ServiceBoprocessActionAcess;
                $model->role_engine_id = $_POST['re_id'];
                $model->action_access = $_POST['action_access'];                
                $model->action_access_label = $_POST['action_access_label'];
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;
                $model->updated_by = Yii::$app->user->id;
                $model->updated_on = date('Y-m-d H:i:s');             
                 
                 // $passREids = explode(',', $_POST['role_to_pass']);
                 // return $passREids;
                if($model->save()){
                    if($_POST['role_to_pass']=='null' || $_POST['role_to_pass']==null){}else{
                        $passREids = explode(',', $_POST['role_to_pass']);
                        foreach ($passREids as $key => $value) {
                         $reModel = ServiceBoprocessRoleEngine::findOne($value);

                           $passtoModel = new ServiceBoprocessActionPassTo;
                           $passtoModel->action_acess_id = $model->id;
                           $passtoModel->passto_role_engine_id = $value;
                           $passtoModel->role_id = $reModel->role_id;
                           $passtoModel->created_on = date('Y-m-d H:i:s');
                           $passtoModel->created_by = Yii::$app->user->id;
                           $passtoModel->updated_by = Yii::$app->user->id;
                           $passtoModel->updated_on = date('Y-m-d H:i:s');
                           $passtoModel->save();
                        }
                    }
                    
                
                    return $this->asJson([
                        'status' => true,
                        'data'=> $this->roleenginedata_withactionacess($model->role_engine_id),
                       
                        'message' => 'Bo User Role Action Access Done',
                        'token' => $token,                    
                    ]);
                }else{
                     return $this->asJson([
                        'status' => false,
                        'message' => json_encode($model->errors),
                        'token' => $token,                    
                    ]);   
                }                   
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

    
   

    protected function roleenginedata($scpm_id){
        $roleEngineData_mapped = [];
        $roleEngineData_remain = [];


        $scpmModel = ServiceConfigParameterMapping::findOne($scpm_id);

        if($scpmModel->bo_workflow){
            $boUserRoleArray = MstUserrole::getallData('BO');

            $mapped_roles  = ServiceBoprocessRoleEngine::find()
            ->alias('re')
            ->joinWith('role mur')
            ->where(['re.scpm_id'=>$scpm_id,'re.is_active'=>1])
            ->andWhere(['not in','mur.role_type',['FO']])
            ->orderBy('re.level_stage_no ASC')->All(); 
        
        $boroles=[];
        foreach ($boUserRoleArray as $key => $value) {
            $boroles[] = [
                'id'=>$value->id,
                'role_id'=>$value->id,
                'role_type'=>$value->role_type,
                'role_name'=>$value->role_name,
                'role_name_label'=>$value->role_name_label,
                'short_code'=>$value->short_code
            ];
         }

        $roleIdsInArray2 = array_column($mapped_roles, 'role_id');  
        $remain_roles = [];

        
        foreach ($boroles as $item) {
            if (!in_array($item['id'], $roleIdsInArray2)) {
                $remain_roles[] = $item;
            }
        }

        $mapped_roles_final = [];
        if($mapped_roles){
            foreach ($mapped_roles as $key => $roleEngine) {
                $mapped_roles_final[] = [
                    're_id'=>$roleEngine->id,
                    'role_id'=>$roleEngine->role_id,
                    'role_name'=>($roleEngine->role_id ? $roleEngine->role->role_name : "NA"),
                    'role_name_label'=>($roleEngine->role_id ? $roleEngine->role->role_name_label : "NA"),
                    'level_stage_no' => $roleEngine->level_stage_no
                ];
            }
        }

        return ['roleEngineData_remain'=>$remain_roles,'roleEngineData_mapped'=>$mapped_roles_final];


        }else{
            return ['roleEngineData_remain'=>[],'roleEngineData_mapped'=>[]];
        }
        

    }

    protected function roleenginedata_withactionacess($re_id){
        $roleEngine  = ServiceBoprocessRoleEngine::find()->where(['id'=>$re_id])->one();
        $scpmModel = ServiceConfigParameterMapping::findOne($roleEngine->scpm_id);
       

        if($roleEngine){

        $rac_mapped = [];
        $roleActionAccess = ServiceBoprocessActionAcess::find()->where(['role_engine_id'=>$roleEngine->id,'is_active'=>1])->all(); 
        foreach ($roleActionAccess as $key => $value) {
            $passto = ServiceBoprocessActionPassTo::find()->where(['action_acess_id'=>$value->id,'is_active'=>1])->all();
            $passtotext = [];
            foreach ($passto as $k => $val) {
                $passtotext[] = $val->role->role_name_label;
            }
            $rac_mapped[] = [
                'id' => $value->id,
                'action_access_name' => $value->action_access,
                'action_access_label' => $value->action_access_label,
                'pass_to_data' => $passto,
                'passtotext' => implode(',', $passtotext)
            ];
        }
        $data = [            
            'mapped_roles'=>[
                'id'=>$roleEngine->id,
                'role_id'=>$roleEngine->role_id,
                'role_name'=>($roleEngine->role_id ? $roleEngine->role->role_name : "Not Set"),
                'role_name_label'=>($roleEngine->role_id ? $roleEngine->role->role_name_label : "Not Set"),
                'level_stage_no' => $roleEngine->level_stage_no,                
                'total_app_day_to_role' => $roleEngine->maxday_fpa
            ],
            'actions_acess_mapped'=>$rac_mapped
        ];              
                            
        return $data;
            
        }else{
              return null; 
        }   
    }



    public function actionGetRoleActionAccess(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['re_id']) && $_POST['re_id']){
               $roleEngine = $this->roleenginedata_withactionacess($_POST['re_id']);

if($roleEngine){


$mapped_formfields = $command->createCommand("SELECT * FROM service_boprocess_form_fields_mapping 
    WHERE role_engine_id =".$_POST['re_id'])->queryAll();
$ids = [];
foreach ($mapped_formfields as $key => $value) {
    $ids[] = $value['ff_id'];   
}

    $ids = implode(',', $ids);
    $not_in_cond ="";
    if($ids){
        $not_in_cond = "AND id not in ($ids)";
    }
$remain_formfields = $command->createCommand("SELECT * FROM mst_form_fields WHERE is_active=1 $not_in_cond")->queryAll();


$ffm_array = ServiceBoprocessFormFieldsMapping::getallFormFieldsOnSection($_POST['re_id']);

                    return $this->asJson([
                        'status' => true,
                        'data' => $roleEngine,
                        // 'allactions' => ServiceBoprocessActionAcess::getallActions($_POST['re_id']),
                        'allactions'=>Yii::$app->params['bo_users_actions'],
                        'rolestopass_array' => ServiceBoprocessActionAcess::getallroleToPass($_POST['re_id']),
                        'allFormFieldArray' => $remain_formfields,
                        'alldataType' => FieldDatatype::find()->all(),
                        'ffm_array' => $ffm_array,
                        'message' => 'Bo User Role Action Access DATA',
                        'token' => $token,                    
                    ]);
               }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => 'data not found for role engine',
                        'token' => $token,                    
                    ]); 
               }

                                
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

    public function actionDeleteRoleActionAccess(){
        
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['aa_id']) && $_POST['aa_id']){
                $cdate = date('Y-m-d H:i:s');
                $user_id = Yii::$app->user->id;
                $model = ServiceBoprocessActionAcess::find()->where(['id'=>$_POST['aa_id']])->one();
                if($model){

                    $sql = "UPDATE service_boprocess_action_pass_to SET is_active = 0 , updated_on = '$cdate', updated_by = $user_id WHERE action_acess_id = :action_acess_id AND is_active=1";

                    Yii::$app->db->createCommand($sql)
                    ->bindValue(':action_acess_id', $_POST['aa_id'])
                    ->execute();

                    $model->is_active = 0;
                    $model->updated_on = $cdate;
                    $model->updated_by = $user_id;
                    $model->save();

                    return $this->asJson([
                        'status' => true,
                        'message' => 'Action Access was deleted',
                        'data'=> $this->roleenginedata_withactionacess($model->role_engine_id),
                        'token' => $token,                    
                    ]);
               }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => 'something went wrong',
                        'token' => $token,                    
                    ]); 
               }                               
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

    
    
    protected function updateTabPreference($scpm_id){
        $model = ServiceTabMapping::find()->where(['scpm_id'=>$scpm_id,'tab_type_id'=>1,'is_active'=>1])->all();
        if($model){
            foreach ($model as $key => $value) {
                $value->preference_order = $key +1;
                $value->save();
            }

            $modeltwo = ServiceTabMapping::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->andWhere(['not in','tab_type_id',[1]])->all();
            if($modeltwo){
                $maxid = Yii::$app->db->createCommand("SELECT count(id) as maxp from service_tab_mapping WHERE scpm_id=$scpm_id AND is_active=1 AND tab_type_id=1")->queryScalar();

                foreach ($modeltwo as $key => $value) {
                    $maxid = $maxid+1;
                    $value->preference_order = $maxid;
                    $value->save();
                    
                }
            }
        }
        return true;
    }

    

    public function actionUpdateworkflowconfig(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $cdate = date('Y-m-d H:i:s');
            $user_id = Yii::$app->user->id;
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id']) && $_POST['scpm_id'] && isset($_POST['bo_workflow']) && $_POST['bo_workflow']){
                $model = ServiceConfigParameterMapping::find()->where(['id'=>$_POST['scpm_id']])->one();
                if($model){
                    $model->bo_workflow = $_POST['bo_workflow'];
                    $model->is_deemed_approved = $_POST['is_deemed_approved'];
                    $model->total_application_days = $_POST['total_application_days'];
                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = Yii::$app->user->id;

                    if($model->save()){
                        // $reModel = ServiceBoprocessRoleEngine::find()->where(['scpm_id'=>$model->id,'is_active'=>1])->all();
                        // if($reModel){
                        //     foreach ($reModel as $key => $value) {
                        //         $sql = "UPDATE service_boprocess_action_acess SET is_active = 0 , updated_on = '$cdate', updated_by = $user_id WHERE role_engine_id = :role_engine_id AND is_active=1";

                        //         Yii::$app->db->createCommand($sql)
                        //         ->bindValue(':role_engine_id', $value->id)
                        //         ->execute();
                        //         $value->is_active = 0;
                        //         $value->updated_on = date('Y-m-d H:i:s');
                        //         $value->updated_by = Yii::$app->user->id;
                        //         $value->save();
                        //     }
                        // }
                        return $this->asJson([
                            'status' => true,      
                            'roleEngineData' => $this->roleenginedata($model->id),                   
                            'message' => 'Workflow has been change for this service',
                            'token' => $token,                    
                        ]);
                    }else{
                         return $this->asJson([
                            'status' => false,
                            'message' => json_encode($model->errors),
                            'token' => $token,                    
                        ]);   
                    }  
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => 'No data found',
                        'token' => $token,      
                        
                    ]);
                }                          
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


    public function actionAddformfieldonbo(){
        $command = Yii::$app->db;
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['re_id']) && $_POST['re_id'] && isset($_POST['field_datatype_id']) && $_POST['field_datatype_id']){

            $model = new ServiceBoprocessFormFieldsMapping;
            $model->role_engine_id = $_POST['re_id'];
            $model->field_datatype_id = $_POST['field_datatype_id'];
            $model->preference_order  = ServiceBoprocessFormFieldsMapping::getnextPreferenceorderno($_POST['re_id']);
                if($_POST['ff_id']=='undefined' || $_POST['ff_id']==null || $_POST['ff_id']=='null'){               
// before add new form fields first check if already exist

$checkff = FormFields::find()->where(['form_field_name'=>$_POST['field_name']])->one();    
if($checkff){
    $model->ff_id = $checkff->id;
}else{
    $ff = New FormFields;
    $ff->form_field_id = FormFields::getformfieldid();
    $ff->form_field_name = $_POST['field_name'];
    $ff->created_on = date('Y-m-d H:i:s');
    $ff->created_by = Yii::$app->user->id; 
    if(!$ff->save()){
        return $this->asJson([
            'status' => false,
            'message' => json_encode($ff->errors),
            'token' => $token,                    
        ]); 
    }
    $model->ff_id = $ff->id;  
}               
                    
                    
                    
                }else{
                     $model->ff_id = $_POST['ff_id'];        
                }
                $model->field_name = $_POST['field_name'];

                if($_POST['is_required']=='null'){
                    $model->is_required = 0;
                }else{
                    $model->is_required = 1;
                }

                if($_POST['placeholder']=='undefined' || $_POST['placeholder']=='null'){
                    $model->placeholder = $model->field_name;
                }else{
                    $model->placeholder = $_POST['placeholder'];
                }

                $model->is_visible_for_role_engine_id = $_POST['is_visible_for_role_engine_id'];
               
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;   
                $model->updated_on = date('Y-m-d H:i:s');
                $model->updated_by = Yii::$app->user->id;  
                    
                if($model->save()){
                     
                     $re_id = $model->role_engine_id;
                     $mapped_formfields = $command->createCommand("SELECT * FROM service_boprocess_form_fields_mapping 
    WHERE role_engine_id =".$re_id)->queryAll();
$ids = [];
foreach ($mapped_formfields as $key => $value) {
    $ids[] = $value['ff_id'];   
}

    $ids = implode(',', $ids);
    $not_in_cond ="";
    if($ids){
        $not_in_cond = "AND id not in ($ids)";
    }
$remain_formfields = $command->createCommand("SELECT * FROM mst_form_fields WHERE is_active=1 $not_in_cond")->queryAll();

                    return $this->asJson([
                        'status' => true,
                        'message' => 'Form Field Mapping done',
                        're_id' => $re_id,
                        'allFormFieldArray' => $remain_formfields,
                        'ffm_array' =>  ServiceBoprocessFormFieldsMapping::getallFormFieldsOnSection($re_id),
                        'token' => $token,            
                                 
                    ]);

        }else{
            return $this->asJson([
                    'status' => false,
                    'message' => 'NO Data Found Something went wrong',
                    'token' => $token,      
                    
                ]);
        }
    }else{
            return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing action for',
                    'token' => $token,      
                    
                ]);
        }
    }

    public function actionDeleteFfmOnrole(){
        $command = Yii::$app->db;
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['id']) && $_POST['id']){

            $model = ServiceBoprocessFormFieldsMapping::find()->where(['id'=>$_POST['id']])->one();                
                if($model){
                     
                     $re_id = $model->role_engine_id;
                     $model->delete();
                     $mapped_formfields = $command->createCommand("SELECT * FROM service_boprocess_form_fields_mapping 
    WHERE role_engine_id =".$re_id)->queryAll();
$ids = [];
foreach ($mapped_formfields as $key => $value) {
    $ids[] = $value['ff_id'];   
}

    $ids = implode(',', $ids);
    $not_in_cond ="";
    if($ids){
        $not_in_cond = "AND id not in ($ids)";
    }
$remain_formfields = $command->createCommand("SELECT * FROM mst_form_fields WHERE is_active=1 $not_in_cond")->queryAll();

                    return $this->asJson([
                        'status' => true,
                        'message' => 'Form Field Mapping deleted',
                        'allFormFieldArray' => $remain_formfields,
                        'ffm_array' =>  ServiceBoprocessFormFieldsMapping::getallFormFieldsOnSection($re_id),
                        'token' => $token,            
                                 
                    ]);

        }else{
            return $this->asJson([
                    'status' => false,
                    'message' => 'NO Data Found Something went wrong',
                    'token' => $token,      
                    
                ]);
        }
    }else{
            return $this->asJson([
                    'status' => false,
                    'message' => 'Parameter missing action for',
                    'token' => $token,      
                    
                ]);
        }
    }

    public function actionConfirmandsubmit(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['scpm_id']) && $_POST['scpm_id']){
                $model = ServiceConfigParameterMapping::find()->where(['id'=>$_POST['scpm_id']])->one();
                if($model){
                    $model->is_workflow_done = $_POST['status'];
                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = Yii::$app->user->id;

                    if($model->save()){
                        if($model->is_workflow_done==1){
                            $msg = 'Congratulation service has been configure and live';
                        }else{
                            $msg = 'This service has been set to maintenance mode.';
                        }
                        return $this->asJson([
                            'status' => true,
                            'model'=>$model,
                            'message' => $msg,
                            'token' => $token,                    
                        ]);
                    }else{
                         return $this->asJson([
                            'status' => false,
                            'message' => json_encode($model->errors),
                            'token' => $token,                    
                        ]);   
                    }  
                }else{
                    return $this->asJson([
                        'status' => false,
                        'message' => 'No data found',
                        'token' => $token,      
                        
                    ]);
                }                          
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
}

?>