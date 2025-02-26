<?php 
namespace app\modules\application\controllers;
    // this is for FO user

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

    class ServiceworkflowController extends RestController{

    	
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
	

        public function actionManage(){
            $command = Yii::$app->db;
            if(Yii::$app->user->id){
                $token = Token::tokenGenerator(Yii::$app->user->id);
                if(isset($_POST['scpm_id'])){
                    $model = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
     
                    $tabs = ServiceTabMapping::find()->where(['scpm_id'=>$model->id,'is_active'=>1])->orderBy('tab_type_id, preference_order ASC')->all();

                    if(isset($_POST['application_id']) && ($_POST['application_id']!=null || $_POST['application_id']!='null')){
                        $application_id = $_POST['application_id'];
                    }else{
                        $application_id = null;
                    }
                
                    foreach ($tabs as $key => $value) {
                        switch ($value->tabType->short_code) {
                            case 'FF':                        
                              
                                $data = $this->getsectiondata($value->id,$application_id);
                                 $tabdetail[$value->tab_name] =$data;
                               
                                break;

                            case 'DMS':
                                $dms_data = ServiceDmsMapping::getDMS_mapped_withUploaded_data_forp($value->scpm_id,$value->id, $value->tab_name,$application_id);
                                
                                 
                                 $tabdetail[$value->tab_name] =$dms_data;        
                                break;

                            case 'DEC':
                                $dec_data = ServiceDeclarationMapping::getDec_mapped_data_forp($value->scpm_id,$value->id,$value->tab_name);
                                
                                $tabdetail[$value->tab_name] = $dec_data;
                                break;

                            case 'MC':
                                $tabdetail[$value->tab_name] = [];
                                break;

                            case 'SIGN':
                                $tabdetail[$value->tab_name] = [];
                                break;

                            case 'PAY':
                                $tabdetail[$value->tab_name] = [];
                                break;

                        }
                    }

                    
                    

                    return $this->asJson([
                        'status' => true,
                        'service_name' => $model->service->service_name,
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

        // public function actionAddMoreSessionSave(){
        //     $command = Yii::$app->db;
        //     if(Yii::$app->user->id){
        //         $token = Token::tokenGenerator(Yii::$app->user->id);
        //         if(isset($_POST['add_more_btn_id_code'])){
        //             $add_more_btn_code = $_POST['add_more_btn_id_code'];
        //             $print_data = [];
        //             //$original_data = [];
        //             if($_POST['new_data']!=''){
        //                 $data = json_decode($_POST['new_data']);                    
        //                 foreach($data as $key=>$val){
        //                     if($key=='select'){
        //                         $option_value_name = OptionValue::getValueByID($val->value);
        //                         $text = $option_value_name;
        //                     }else{
        //                         $text = $val->value;
        //                     }

        //                     $print_data[] = ['form_field_code_id'=>$val->form_field_code_id,'value'=>$val->value,'text'=>$text];   
        //                     //$original_data[] = $val->value;
        //                 }                    
        //             }    

                    
        //             //below code is for data show in view page
        //             $final_print_data = [];
        //             if(Yii::$app->session->has('addmore_'.$add_more_btn_code)){
        //                 $previous_data = Yii::$app->session->get('addmore_'.$add_more_btn_code);
        //                 $final_print_data = array_merge($previous_data,[$print_data]);
        //                 Yii::$app->session->set('addmore_' . $add_more_btn_code, $final_print_data);
        //                 $final_print_data = $final_print_data;
        //             }else{                    
        //                 Yii::$app->session->set('addmore_' . $add_more_btn_code, [$print_data]);
        //                 $final_print_data = [$print_data];
        //             }

                  
        //             return $this->asJson([
        //                 'status' => true,
        //                 'data' => $final_print_data,
        //                 'add_more_btn_id_code' => $_POST['add_more_btn_id_code'],
        //                 'token' => $token              
        //             ]);
        //         }else{
        //             return $this->asJson([
        //             'status' => false, 'message'=>'Parameter missing',
        //             'token' => $token,  
        //         ]);
        //         }            
        //     }else{
        //          return $this->asJson([
        //             'status' => false, 'message'=>'Session expired. Please login again'
        //         ]);
        //     } 
        // }

        // public function actionDeleteAddMoreRow(){
        //     $command = Yii::$app->db;
        //     if(Yii::$app->user->id){
        //         $token = Token::tokenGenerator(Yii::$app->user->id);
        //         if(isset($_POST['add_more_btn_id_code'])){
        //             $add_more_btn_code = $_POST['add_more_btn_id_code'];
        //             $index = $_POST['row_id'];
        //             // below code to print data in view page
        //             unset($_SESSION['addmore_'.$add_more_btn_code][$index]);                
        //             $final_print_data = [];
        //              foreach (Yii::$app->session->get('addmore_'.$add_more_btn_code) as $key => $value) {
        //                  $final_print_data[] = $value;
        //              }
        //              Yii::$app->session->set('addmore_'.$add_more_btn_code,$final_print_data);


        //             return $this->asJson([
        //                 'status' => true,
        //                 'data' => $final_print_data,
        //                 'add_more_btn_id_code' => $_POST['add_more_btn_id_code'],
        //                 'token' => $token              
        //             ]);
        //         }else{
        //             return $this->asJson([
        //             'status' => false, 'message'=>'Parameter missing',
        //             'token' => $token,  
        //         ]);
        //         }            
        //     }else{
        //          return $this->asJson([
        //             'status' => false, 'message'=>'Session expired. Please login again'
        //         ]);
        //     } 
        // }


        public function actionSaveData(){
            $command = Yii::$app->db;
            // return $_POST;
            if(Yii::$app->user->id){

                $token = Token::tokenGenerator(Yii::$app->user->id);

                if(Yii::$app->user->identity->role_type!='FO'){
                    return [
                        'status'=>false,
                        'message' =>'You cannot submit the applicant',
                        'token' => $token
                    ];
                }

                if(isset($_POST['scpm_id']) && isset($_POST['form_fields_data']) && isset($_POST['tab_id'])){
                     
                     if($_POST['form_fields_data']){
                        // $from_fields_data = json_decode($_POST['form_fields_data'], true);

                        $from_fields_data = $_POST['form_fields_data'];
                        if(is_array($from_fields_data) && !empty($from_fields_data)){
                            $config_param = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);

$model = new TApplicationSubmission;

if(isset($_POST['application_id']) && $_POST['application_id']!=null && $_POST['application_id']!='null' ){
    $model = TApplicationSubmission::find()->where(['id'=>$_POST['application_id']])->one();
}else{
    $model->service_id = $config_param->service_id;
    $model->scpm_id = $config_param->id;
    $model->created_on = date('Y-m-d H:i:s');
    $model->state_id = Yii::$app->user->identity->state_id;
    $model->district_id = Yii::$app->user->identity->district_id;
}                         
                            
                            

        $model->form_field_data = json_encode($from_fields_data);
        $model->application_status = 'D'; //Draft Stage
        $model->sso_user_id = Yii::$app->user->id;
        $model->where_app_is_role_id = Yii::$app->user->identity->role_id;
        $model->updated_on = date('Y-m-d H:i:s');
        if($model->save()){
            $comment = 'application was submitted.';
        $this->updateapplicationstatus_insertlog($model->id,$comment,$_POST['tab_id']);
            return $this->asJson([
                                'status' => true,
                                'application' => $model->id,
                                'token' =>$token              
                            ]);
        }else{
            return $this->asJson([
                                'status' => false,
                               'message' => json_encode($model->errors),
                                'token' =>$token              
                            ]);
        }

                            
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

public function actionGetApplicationData(){
    $token = Token::tokenGenerator(Yii::$app->user->id);

    // if(Yii::$app->user->identity->role_type!='FO'){
    //     return [
    //         'status'=>false,
    //         'message' =>'You cannot submit the applicant',
    //         'token' => $token
    //     ];
    // }

        if(isset($_POST['application_id'])){
            $model = TApplicationSubmission::findOne($_POST['application_id']); 
             if($model){
                


                    return $this->asJson([
                        'status' => true,
                        'model' => $model,
                        'token' =>$token              
                    ]);

                    

                }else{
                    return $this->asJson([
                        'status' => false, 
                        'message'=>'No Data Found',
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

    //     public function actionSaveupdateData(){
    //         $command = Yii::$app->db;
    //         if(Yii::$app->user->id){
    //             $token = Token::tokenGenerator(Yii::$app->user->id);
    //              if(Yii::$app->user->identity->role_type!='FO'){
    //                 return [
    //                     'status'=>false,
    //                     'message' =>'You cannot submit the applicant',
    //                     'token' => $token
    //                 ];
    //             }
    //             if(isset($_POST['application_id']) && isset($_POST['form_fields_data'])){
                     
    //                  if($_POST['form_fields_data']){
    //                     $from_fields_data = json_decode($_POST['form_fields_data'], true);
    //                     if(is_array($from_fields_data) && !empty($from_fields_data)){
    //                         $config_param = ServiceConfigParameterMapping::findOne($_POST['scpm_id']);
    //                         $current_tab_mapping = ServiceTabMapping::findOne($_POST['tab_id']);

    //                         $model = TApplicationSubmission::findOne($_POST['application_id']);
                           
    //                         $form_fields_final_data = [];
    //                         $session_keys = [];
    //                         foreach ($from_fields_data as $key => $value) {

    //                             $sql = "SELECT ffm.*, dt.type 
    //                             FROM service_form_tab_section_form_fields_mapping ffm
    //                             INNER JOIN mst_form_fields mff ON ffm.ff_id=mff.id 
    //                             INNER JOIN mst_field_datatype dt ON ffm.field_datatype_id=dt.id 
    //                             WHERE mff.form_field_id = :form_field_id";
    //                             $command = Yii::$app->db->createCommand($sql);
    //                             $command->bindValue(':form_field_id', $key);
    //                             $check_is_add_more_field = $command->queryOne(); 

    //                             if(!empty($check_is_add_more_field)){
    //                                 if($check_is_add_more_field['is_add_more_field']==0){
    //                                     if($check_is_add_more_field['type']=='addmore'){
    //                                         if(Yii::$app->session->has('addmore_'.$key)){
    //                                             $form_fields_final_data[$key] = Yii::$app->session->get('addmore_'.$key);
    //                                             $session_keys[] = 'addmore_'.$key;
    //                                         }                                    
    //                                     }else{
    //                                         $form_fields_final_data[$key] = $value;
    //                                     }
    //                                 }     

    //                             }else{
    //                                  return $this->asJson([
    //                                     'status' => false, 
    //                                     'data' => $from_fields_data,
    //                                     'message'=>'Form Field missing there is some internal configuration was changed',
    //                                     'token' => $token,  
    //                                 ]);   
    //                             }
    //                         }

    //                         $previuos_form_field_data = (array) json_decode($model->form_field_data,true);
                            

    //                         if(array_key_exists($_POST['tab_id'], $previuos_form_field_data)){
    //                            $previuos_form_field_data[$_POST['tab_id']] = $form_fields_final_data;
    //                            $final_form_fields_array = $previuos_form_field_data;
    //                         }else{
    //                            $current_tab_form_fields_array[$_POST['tab_id']] = $form_fields_final_data;
    //                            $final_form_fields_array = $previuos_form_field_data+$current_tab_form_fields_array;
    //                         }

                           

    //                         $model->form_field_data = json_encode($final_form_fields_array);
    //                         $model->application_status = 'D'; //Draft Stage
    //                         $model->sso_user_id = Yii::$app->user->id;
    //                         $model->updated_on = date('Y-m-d H:i:s');
    //                         $model->save();

    //                         $comment = $current_tab_mapping->tab_name.' was submitted.';
    //                         $this->updateapplicationstatus_insertlog($model->id,$comment,$_POST['tab_id']);

                            

    // // now check multipal data in session and remove from session
    // if(!empty($session_keys)){
    //     foreach ($session_keys as $s_val) {
    //         Yii::$app->session->remove($s_val);
    //     }
    // }


    //                         return $this->asJson([
    //                             'status' => true,
    //                             'current_tab_id' => $current_tab_mapping->id,
    //                             'token' => $token              
    //                         ]);

                            

    //                     }else{
    //                         return $this->asJson([
    //                             'status' => false, 
    //                             'data' => $from_fields_data,
    //                             'message'=>'Something went wrong while submitting form some fields missing',
    //                             'token' => $token,  
    //                         ]);
    //                     }
    //                  }         
    //             }else{
    //                 return $this->asJson([
    //                 'status' => false, 'message'=>'Parameter missing',
    //                 'token' => $token,  
    //             ]);
    //             }            
    //         }else{
    //              return $this->asJson([
    //                 'status' => false, 'message'=>'Session expired. Please login again'
    //             ]);
    //         }
    //     }

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
                    // save fielname by timestamp with extension            
                                $extension = pathinfo($file_detail->name, PATHINFO_EXTENSION);

                                $file_path = $directory.uniqid().'.'.$extension;
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

                        //$dms_data = ServiceDmsMapping::getDMS_mapped_withUploaded_data_forp($application->scpm_id,$application->id);


            
                    return $this->asJson([
                        'status' => true,
                        'model' => $model,
                        //'dms_data'=>$dms_data,
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

        // public function actionSubmitDms(){
        //     $command = Yii::$app->db;
           
        //     if(Yii::$app->user->id){
        //         $token = Token::tokenGenerator(Yii::$app->user->id);
        //          if(Yii::$app->user->identity->role_type!='FO'){
        //             return [
        //                 'status'=>false,
        //                 'message' =>'You cannot submit',
        //                 'token' => $token
        //             ];
        //         }
        //         if(isset($_POST['application_id'])){

        //             $comment = 'Documents was uploaded.';
        //             $this->updateapplicationstatus_insertlog($_POST['application_id'],$comment,$_POST['tab_id']);
                                
        //             return $this->asJson([
        //                 'status' => true,
        //                 'current_tab_id' => $_POST['tab_id'],
        //                 'token' => $token                   
        //             ]);
        //         }else{
        //             return $this->asJson([
        //             'status' => false, 'message'=>'Parameter missing',
        //             'token' => $token,  
        //         ]);
        //         }            
        //     }else{
        //          return $this->asJson([
        //             'status' => false, 'message'=>'Session expired. Please login again'
        //         ]);
        //     } 
        // }

        // public function actionDecSubmit(){
        //     $command = Yii::$app->db;
        //     if(Yii::$app->user->id){
        //         $token = Token::tokenGenerator(Yii::$app->user->id);
        //          if(Yii::$app->user->identity->role_type!='FO'){
        //             return [
        //                 'status'=>false,
        //                 'message' =>'You cannot submit',
        //                 'token' => $token
        //             ];
        //         }
        //         if(isset($_POST['application_id'])){
        //             $comment = 'Declaration was submitted.';
        //             $this->updateapplicationstatus_insertlog($_POST['application_id'],$comment,$_POST['tab_id']);
                     
        //            return $this->asJson([
        //                 'status' => true,
        //                 'current_tab_id' => $_POST['tab_id'],
        //                 'token' => $token              
        //             ]);
        //         }else{
        //             return $this->asJson([
        //                 'status' => false, 'message'=>'Parameter missing',
        //                 'token' => $token,  
        //             ]);
        //         }            
        //     }else{
        //          return $this->asJson([
        //             'status' => false, 'message'=>'Session expired. Please login again'
        //         ]);
        //     }
        // }

        // this action is for dependent dropdown
        public function actionGetOptionsDependsOnParent(){
            $command = Yii::$app->db;
            if(Yii::$app->user->id){
                $token = Token::tokenGenerator(Yii::$app->user->id);
                if(isset($_POST['selectedValue']) && $_POST['selectedValue']){


                   
                    $chieldOptions = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$_POST['selectedValue']])->all();

                    $records = [];
                    foreach ($chieldOptions as $key => $value) {
                        $records[$value->id] = $value->name;
                    }  
                 
                   return $this->asJson([
                        'status' => true,
                        'data' => $records,
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

public function actionApppdf(){
    $token = Token::tokenGenerator(Yii::$app->user->id);
    if(isset($_POST['application_id']) && ($_POST['application_id']!=null || $_POST['application_id']!='null')){

        $model = TApplicationSubmission::find()->where(['id'=>$_POST['application_id']])->one();

        $main_details = [
            'service_name' => $model->scpm->service->service_name,
            'department' => $model->scpm->dept->dept_name,
            'country' => $model->scpm->country->name,
            'state' => ($model->state_id ? $model->state->name : null),
            'district' => ($model->district_id ? $model->district->name : null),
        ];

        $applicantData = TApplicationSubmission::makeCompleteArrayByUploadData($model->form_field_data);

        return $this->asJson([
            'status' => true, 
            'data' => [
                'main_details' => $main_details,
                'applicantData' => [
                    'form_field_data'=>TApplicationSubmission::fflabels($applicantData['ff']),
                    'dms_data'=>TApplicationDms::getDetails($model->id)
                ],
            ],
            'message'=>'now print your application',
            'token' => $token,  
        ]);

    }else{
         return $this->asJson([
            'status' => false, 'message'=>'Parameter missing',
            'token' => $token,  
        ]);
    }
}   

public function actionCertificate(){
    $token = Token::tokenGenerator(Yii::$app->user->id);
    if(isset($_POST['application_id']) && ($_POST['application_id']!=null || $_POST['application_id']!='null')){

        $model = TApplicationSubmission::find()->where(['id'=>$_POST['application_id']])->one();
    $htmlData = $this->renderPartial('application_certificate',
        [
            'title' => 'Application Certificate PDF',
            'model'=>$model
        ]);
    return [
                'status'=>true,
                'message'=>'Certificate is print',
                'htmlData' => $htmlData,
                'token' => $token
    ];
    }else{
         return $this->asJson([
            'status' => false, 'message'=>'Parameter missing',
            'token' => $token,  
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

        protected function getsectiondata($tab_id,$application_id){
            $data = [];
            $section_data = ServiceFormTabSectionMapping::find()->where(['stm_id'=>$tab_id,'is_active'=>1])->orderBy('preference_order ASC')->all();

            foreach ($section_data as $key => $value) {           

                $field_query = ServiceFormTabSectionFormFieldsMapping::find()
                ->alias('sftsfm')
                ->joinWith('fdt f')
                ->where(['sftsfm.sftsm_id'=>$value->id,'sftsfm.is_active'=>1,'sftsfm.is_add_more_field'=>0])
                ->andWhere(['not in','f.type',['addmore']])
                ->orderBy('sftsfm.preference_order ASC')->all();

                $fields_data = $this->getFields($field_query,$value,$application_id);

               $repeater_query = \app\models\ServiceFormTabSectionFormFieldsMapping::find()
                    ->alias('sftsfm')  
                    ->joinWith('fdt f')  
                    ->where([
                        'sftsfm.sftsm_id' => $value->id,
                        'sftsfm.is_active' => 1,
                        'f.type'=>'addmore',
                    ])->All();

                
                  
                $repeater_field_data = $this->getaddmoremappingfields($repeater_query,$value,$application_id);
                 

                   $data[] = [
                    'tab_id' => $value->stm_id,
                    'category'=>$value->sc->section,
                    'fields'=>$fields_data,
                    'repeaters'=>$repeater_field_data
                ];
            }
            return $data;
        }

// sts_mapp service tab section mapping
    protected function getFields($field_data,$sts_mapp,$application_id){
         $fields_data=[];
           foreach ($field_data as $k => $val) {
                 
            $options = NULL;
            if($val->option_master_id){
                $options = $this->getOptions($val);
            }

$dynamic_options_dt = FieldDatatype::GetOptionsfieldtype();

if($application_id!=null && ($val->option_master_id==null || $val->option_master_id=='') && in_array($val->fdt->type, $dynamic_options_dt) && $val->depends_on_sftsffm_id){

    $parentFieldCodeId = $val->sftsffm->ff->form_field_id;
    $appData = TApplicationSubmission::find()->where(['id'=>$application_id])->one();

    $fielddecode = (array) json_decode($appData->form_field_data,true);
    $parentValue = TApplicationSubmission::FindEachValue($fielddecode,$parentFieldCodeId);
 
   
    $chieldOptions = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$parentValue])->all();

    
    foreach ($chieldOptions as $key => $value) {
        $options[$value->id] = $value->name;
    } 
    
}


    $child_fields_depends_on_me = Yii::$app->db->createCommand("
    SELECT ff.form_field_id as ff_id From service_form_tab_section_form_fields_mapping ffm
    INNER JOIN mst_form_fields ff on ff.id=ffm.ff_id
    WHERE ffm.is_active=1 AND ffm.depends_on_sftsffm_id=".$val->id)->queryOne();

            $conditions = null;
            if($val->option_master_id && $child_fields_depends_on_me){
                $conditions['dependantValues'] = [
                    'endPoint' => 'serviceworkflow/get-options-depends-on-parent',
                    'autoPopulateField' => [
                        [
                            'getKey' => 'data',
                            'requestKey' => 'selectedValue',
                            'key' => $child_fields_depends_on_me['ff_id'],
                            'apiCall' => true,
                            // "stepName"=> $sts_mapp->stm->tab_name,
                            // "category"=> $sts_mapp->sc->section,
                            // "repeaterKey"=> '',
                            // "repeaterIndex"=> 0
                        ]
                    ]
                ];
            }


                $fields_data[]=[
                    'id' => $val->id,
                    'id_desc' => 'form_field_mapping_id',
                    'key'=>$val->ff->form_field_id,
                    'field_name'=>$val->field_name,
                    'type'=>$val->fdt->type,
                    'disabled' => false,
                    'required'=>$val->is_required ? true : false,
                    'placeholder'=>($val->placeholder ? $val->placeholder : $val->field_name),
                    'values'=> $options,
                    'conditions' => $conditions
                ];
           }

           return $fields_data;
        }

// sts_mapp service tab section mapping
        protected function getaddmoremappingfields($addMoreBtnModel,$sts_mapp,$application_id){
            $final_array = [];
            if($addMoreBtnModel){
                foreach ($addMoreBtnModel as $addmore) {
                    $sql = "SELECT 
                    am.id as addmoremappingid, 
                    ffm.*, 
                    ff.form_field_id as form_field_code_id, 
                    ft.type as field_type 
                    FROM service_form_field_add_more_mapping am 
                    INNER JOIN service_form_tab_section_form_fields_mapping ffm ON am.form_field_id = ffm.id
                     INNER JOIN mst_form_fields ff ON ffm.ff_id = ff.id
                     INNER JOIN mst_field_datatype ft ON ffm.field_datatype_id = ft.id
                     WHERE am.is_active=1 AND am.add_more_field_id = :add_more_field_id ORDER BY ffm.preference_order ASC";
                    $command = Yii::$app->db->createCommand($sql);
                    $command->bindValue(':add_more_field_id', $addmore->id);
                    $data = $command->queryAll();

                    foreach ($data as $k => $val) {
                        $val = (object) $val;
                        $options = NULL;
                        if($val->option_master_id){
                            $options = $this->getOptions($val);
                        }


$dynamic_options_dt = FieldDatatype::GetOptionsfieldtype();

if($application_id!=null && ($val->option_master_id==null || $val->option_master_id=='') && in_array($val->field_type, $dynamic_options_dt) && $val->depends_on_sftsffm_id){


    $parentFieldCodeData = ServiceFormTabSectionFormFieldsMapping::findOne($val->depends_on_sftsffm_id);

    $parentFieldCodeId = $parentFieldCodeData->ff->form_field_id;
    $appData = TApplicationSubmission::find()->where(['id'=>$application_id])->one();

    $fielddecode = (array) json_decode($appData->form_field_data,true);
    $parentValue = TApplicationSubmission::FindEachValue($fielddecode,$parentFieldCodeId);
 
   
    $chieldOptions = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$parentValue])->all();

    
    foreach ($chieldOptions as $key => $value) {
        $options[$value->id] = $value->name;
    } 
    
}                        


                    $child_fields_depends_on_me = Yii::$app->db->createCommand("
                    SELECT ff.form_field_id as ff_id From service_form_tab_section_form_fields_mapping ffm
                    INNER JOIN mst_form_fields ff on ff.id=ffm.ff_id
                    WHERE ffm.is_active=1 AND ffm.depends_on_sftsffm_id=".$val->id)->queryOne();

                    $conditions = null;
                    if($val->option_master_id && $child_fields_depends_on_me){
                        $conditions['dependantValues'] = [
                            'endPoint' => 'serviceworkflow/get-options-depends-on-parent',
                            'autoPopulateField' => [
                                [
                                    'getKey' => 'data',
                                    'requestKey' => 'selectedValue',
                                    'key' => $child_fields_depends_on_me['ff_id'],
                                    'apiCall' => true,
                                    // "stepName"=> $sts_mapp->stm->tab_name,
                                    // "category"=> $sts_mapp->sc->section,
                                    // "repeaterKey"=> '',
                                    // "repeaterIndex"=> 0
                                ]
                            ]
                        ];
                    }


                        $fields_data[]=[
                            'id' => $val->id,
                            'id_desc' => 'form_field_mapping_id',
                            'key'=>$val->form_field_code_id,
                            'field_name'=>$val->field_name,
                            'type'=>$val->field_type,
                            'disabled' => false,
                            'required'=>$val->is_required ? true : false,
                            'placeholder'=>($val->placeholder ? $val->placeholder : $val->field_name),
                            'values'=> $options,
                            'conditions' => $conditions
                        ];
                   }

                    $final_array[$addmore->ff->form_field_name] = [$fields_data];
                }

                return $final_array;
            }
                return NULL;
            
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

            // if(empty($records)){
            //     if($val->static_options){
            //         $statciOptionArray = explode(',', $val->static_options);
            //         if(is_array($statciOptionArray)){
            //             foreach ($statciOptionArray as $key => $value) {
            //                 $value = trim($value);
            //                 $records[$value] = $value;
            //             }
            //         }
            //     }
            // }

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

                //check bo user role engine is applicable or not for this
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
            }
            
            $application->updated_on = date('Y-m-d H:i:s');
            $application->save();
        }       
    }

    ?>