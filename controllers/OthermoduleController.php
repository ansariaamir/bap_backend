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
use app\models\masters\FieldDatatype;
use app\models\transactions\IncentiveFields;
use app\models\transactions\IncentiveData;
use app\models\transactions\KnowyourapprovalOptions;
use app\models\transactions\KnowyourapprovalQuestions;
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

class OthermoduleController extends Controller
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
                'search-incentive','submitincentiveforsearch','getincentivequestions','getcheckmappedquestion'
            ],
        ];

        return $behaviors;
    }

    public function actionIncentive(){
        
        $token = Token::tokenGenerator(Yii::$app->user->id);
        $getincentivecolums = $this->getincentivecolums();  
        $form_data = [
            'alldataType' => FieldDatatype::find()->all(),
            'options_masters' =>  \app\models\masters\Option::find()->where(['is_active'=>1])->all(),
        ]; 

        
        return $this->asJson([
            'status' => true, 
            'form_data' => $form_data,
            'getincentivecolums'=>$getincentivecolums,
            'token'=>$token
        ]);       
    }

   

   

   public function actionAddincentiveformfields(){
        $token = Token::tokenGenerator(Yii::$app->user->id);

        if(isset($_POST['field_name']) && $_POST['field_datatype_id'] && $_POST['field_name']){

            $model = new IncentiveFields;
            $model->type_id = $_POST['field_datatype_id'];
            $model->field_name = $_POST['field_name'];
            if(Helper::CheckNotEmptyCondition($_POST['is_required'])){
                    $model->is_required = 1;
                }else{
                    $model->is_required = 0;
                }
            if($_POST['option_master_id']=='undefined' || $_POST['option_master_id']=='null'){
                    $model->option_id = null;
                }else{
                    $model->option_id = $_POST['option_master_id'];
                }

                if($_POST['placeholder']=='undefined' || $_POST['placeholder']=='null'){
                    $model->placeholder = $model->field_name;
                }else{
                    $model->placeholder = $_POST['placeholder'];
                }
          
           
            if(Helper::CheckNotEmptyCondition($_POST['show_for_search'])){
                $model->show_for_search = 1;
            }else{
                $model->show_for_search = 0;
            }
            if($model->show_for_search==1){
                $model->search_label = isset($_POST['search_label']) ? $_POST['search_label'] : null;
            }else{
                $model->search_label = null;
            }
            
            $model->created_by = Yii::$app->user->id;
            $model->updated_by = Yii::$app->user->id;
            $model->created_on = date('Y-m-d H:i:s');
            $model->updated_on = date('Y-m-d H:i:s');
            if(!$model->save()){
                return print_r($model->errors);
            }            
        }

        $getincentivecolums = $this->getincentivecolums();  
        $form_data = [
            'alldataType' => FieldDatatype::find()->all(),
            'options_masters' =>  \app\models\masters\Option::find()->where(['is_active'=>1])->all(),
        ]; 
        
        return $this->asJson([
            'status' => true, 
            'form_data' => $form_data,
            'message' => 'form field mapped for incentive',
            'getincentivecolums'=>$getincentivecolums,
            'token'=>$token
        ]);  
   }

    protected function getincentivecolums(){        
       $incentiveFieldsMapped = IncentiveFields::find()->where(['is_active'=>1])->all();   
       $records = [];
       foreach ($incentiveFieldsMapped as $key => $value) {
           $records[] = [
            'field_name' => $value->field_name,
            'field_type' => ($value->type_id ? $value->fdt->type : null ),
            'option_master' => ($value->option_id ? $value->option->name : null ),
            'placeholder' => $value->placeholder,
            'is_required' => $value->is_required,
            'show_for_search' => $value->show_for_search,
            'search_label' => $value->search_label,
           ];
       }
       return  $records;       
    }

   public function actionManageIncentive(){
    $token = Token::tokenGenerator(Yii::$app->user->id);
    $incentiveFieldsMapped = IncentiveFields::find()->where(['is_active'=>1])->all();
    $fields_data=[];
        foreach ($incentiveFieldsMapped as $key => $val) {
            $options = NULL;
            if($val->option_id){
                $options = $this->getOptions($val);
            }
            $fields_data[]=[
               
                'ff_id'=>$val->id,
                'field_name'=>$val->field_name,
                'field_type'=>$val->fdt->type,
                'is_required'=>$val->is_required,
                'placeholder'=>$val->placeholder,
                'options'=> $options,
                'value'=>null
            ];
       }

       return $this->asJson([
            'status' => true,
            'fields_data'=>$fields_data,
            'incentive_data' => $this->incentiveGridData(),
            'token'=>$token
        ]); 
    }

    protected function getOptions($val){        

        $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'option_id'=>$val->option_id])->all();
        $records = [];
        foreach ($data as $key => $value) {
            $records[$value->id] = $value->name;
        }

        return $records;
    }

    public function actionSaveincentive(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['form_fields_data']) && $_POST['form_fields_data']){
           
           
                $model = new IncentiveData;
                $model->form_field_data = $_POST['form_fields_data'];
                $model->created_by = Yii::$app->user->id;
                $model->updated_by = Yii::$app->user->id;
                $model->created_on = date('Y-m-d H:i:s');
                $model->updated_on = date('Y-m-d H:i:s');
                if(!$model->save()){
                    return print_r($model->errors);
                }   

                return $this->asJson([
                    'status' => true, 
                    'incentive_data' => $this->incentiveGridData(),
                    'message' => 'incentive added',
                    'token'=>$token
                ]); 

            
        }
    }
    
    public function actionUpdateincentive(){
        
    }

    protected function incentiveGridData(){
       
        $header_data = IncentiveFields::find()->where(['is_active'=>1])->all();
        $header = $header_check = [];
        foreach ($header_data as $key => $value) {
           $header[] = $value->field_name;
           $header_check[$value->id] = $value; 
       }

        $data = IncentiveData::find()->where(['is_active'=>1])->all();
        $grid_data = [];
        foreach ($data as $key => $value) {
            $form_field_data = (array) json_decode($value->form_field_data,true);
            $row_data = [];
            foreach ($header_check as $k => $hv) {
                $field_value = null;
                if(isset($form_field_data[$k])){
                    if($hv->option_id){
                        $ov = OptionValue::findOne($form_field_data[$k]);
                        $field_value = $ov->name;
                    }else{
                        $field_value = $form_field_data[$k];
                    }
                }
                
                $row_data[] = [
                    'id' => $value->id,
                    'field_id' => $k,
                    'field_value'=>$field_value
                ];
            }
            $grid_data[] = $row_data;
        }

        return [
            'header'=>$header,
            'grid_data'=>$grid_data
        ];
    }

    public function actionSearchIncentive(){
        $incentiveFieldsMapped = IncentiveFields::find()->where(['is_active'=>1,'show_for_search'=>1])->all();
        $fields_data=[];
        foreach ($incentiveFieldsMapped as $key => $val) {
            $options = NULL;
            if($val->option_id){
                $options = $this->getOptions($val);
            }
            $fields_data[]=[               
                'ff_id'=>$val->id,
                'field_name'=>$val->field_name,
                'field_type'=>$val->fdt->type,
                'is_required'=>$val->is_required,
                'placeholder'=>$val->placeholder,
                'options'=> $options,
                'value'=>null
            ];
       }

       return $this->asJson([
            'status' => true,
            'fields_data'=>$fields_data
            
        ]); 
    }

    public function actionGetincentivequestions(){
        $old_id = isset($_GET['old_id']) ? $_GET['old_id'] : 0;
        if($old_id>0){
            $incentiveFieldsMapped = IncentiveFields::find()
                ->where(['is_active'=>1,'show_for_search'=>1])
                 ->andWhere(['>','id', $old_id]) 
                ->orderBy(['id' => SORT_ASC])
                ->one();
        }else{
            $incentiveFieldsMapped = IncentiveFields::find()
            ->where(['is_active'=>1,'show_for_search'=>1])
            ->orderBy(['id' => SORT_ASC])
            ->one();
        }
        
        $fields_data=[];
        if($incentiveFieldsMapped){
            
            
            $dt_ar = [2,3,4,5];
            if($incentiveFieldsMapped->option_id && in_array($incentiveFieldsMapped->type_id, $dt_ar)){

                $options = $this->getOptions($incentiveFieldsMapped);
                $answer = '<div class="radio-group">';

                foreach($options as $k=>$v){
                    $answer .= '<label><input type="radio" name="'.$incentiveFieldsMapped->id.'" value="'.$k.'"> '.$v.'</label>';
                }
                $answer .= '</div> <span class="btn btn-primary mt-2" onclick=nextquestion('.$incentiveFieldsMapped->id.')>Next</span>';

                $fields_data=[               
                'ff_id'=>$incentiveFieldsMapped->id,
                'field_name'=>$incentiveFieldsMapped->field_name,
                'search_label'=>$incentiveFieldsMapped->search_label,
                'answers' => $answer
            ];
            }else{
                $fields_data=[               
                'ff_id'=>$incentiveFieldsMapped->id,
                'field_name'=>$incentiveFieldsMapped->field_name,               
                'search_label'=>$incentiveFieldsMapped->search_label,
                'answers' => '<div class="radio-group"> <input type="text" name="'.$incentiveFieldsMapped->id.'"/>  </div> <span class="btn btn-primary mt-2" onclick=nextquestion('.$incentiveFieldsMapped->id.')>Next</span>'
            ];
            }
            
        }
        

       return $this->asJson([
            'status' => true,
            'fields_data'=>$fields_data
            
        ]); 
    }

    public function actionSubmitincentiveforsearch(){
        if(isset($_POST['search_fields_data']) && $_POST['search_fields_data']){
            $search_data = [];

             $searchFieldsData = json_decode($_POST['search_fields_data'], true);

             // modal code
            
            $conditions = [];
            $params = [];

            foreach ($searchFieldsData as $key => $value) {
                $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(form_field_data, '$.$key')) = :value_$key";
                $params[":value_$key"] = $value;
            }

            $whereClause = implode(' OR ', $conditions);

            // Use a query to find matching records
            $query = (new \yii\db\Query())
                ->select('*')
                ->from('incentive_data') // Replace with your actual table name
                ->where($whereClause)
                ->addParams($params);

            $searchresult = $query->all(); // or $query->one() for a single result

        $header_data = IncentiveFields::find()->where(['is_active'=>1])->all();
        $header = $header_check = [];
        foreach ($header_data as $key => $value) {
           $header[] = $value->field_name;
           $header_check[$value->id] = $value; 
       }

      
        $grid_data = [];
        foreach ($searchresult as $key => $value) {
            $form_field_data = (array) json_decode($value['form_field_data'],true);
            $row_data = [];
            foreach ($header_check as $k => $hv) {
                $field_value = null;
                if(isset($form_field_data[$k])){
                    if($hv->option_id){
                        $ov = OptionValue::findOne($form_field_data[$k]);
                        $field_value = $ov->name;
                    }else{
                        $field_value = $form_field_data[$k];
                    }
                 }   
                $row_data[] = [
                   'field_value'=>$field_value
                ];
            }
            $grid_data[] = $row_data;
        }

        $grid_data =  [
            'header'=>$header,
            'grid_data'=>$grid_data
        ];
       
            return $this->asJson([
                'status' => true,
                'incentive_data' => $grid_data,
                
            ]); 
        }else{
            return $this->asJson([
                'status' => false,
                'message' => 'Parameter missing',
                
            ]);
        }
    }

    /************** Know Your Approval ***********/

    public function actionManageKnowYourApproval(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        $data = $this->getkyadata();

        
        return $this->asJson([
            'status' => true,    
            'alldataType' => FieldDatatype::find()->all(), 
            'kyadata' => $data,       
            'token'=>$token
        ]);   
    }

    public function actionAddKyaQuestion(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        
        if(isset($_POST['question']) && $_POST['question'] && Helper::CheckNotEmptyCondition($_POST['question'])){
            $model = new KnowyourapprovalQuestions;
            $model->questions = $_POST['question'];
            $model->preference_order = KnowyourapprovalQuestions::getnextPreferenceorderno();
            if(Helper::CheckNotEmptyCondition($_POST['o_id'])){
                 $model->depend_option_id = $_POST['o_id'];
            }
           
            $model->field_type_id = $_POST['field_type_id'];
            $model->created_by = Yii::$app->user->id;
            $model->updated_by = Yii::$app->user->id;
            $model->created_on = date('Y-m-d H:i:s');
            $model->updated_on = date('Y-m-d H:i:s');
            if(!$model->save()){
                return print_r($model->errors);
            }   

            if($model->depend_option_id){
                $Omodel = KnowyourapprovalOptions::findOne($model->depend_option_id);
                if($Omodel){
                    $Omodel->drive_question_id = $model->id;
                    $Omodel->updated_by = Yii::$app->user->id;         
                    $Omodel->updated_on = date('Y-m-d H:i:s');
                    $Omodel->save();
                }                
            }

            return $this->asJson([
                'status' => true,    
                'q_id' => $model->id,       
                'token'=>$token
            ]); 

        }else{
             return $this->asJson([
                'status' => false,
                'message' => 'Parameter missing',
                
            ]);   
        }        
    }

    protected function getkyadata(){
        $sql = "SELECT q.id as q_id, q.questions, q.preference_order as question_no, o.id as o_id, o.options, o.preference_order as option_no, s.service_name  , ft.type as fieldtype, dq.preference_order as dq_no
                FROM knowyourapproval_questions as q
                LEFT JOIN mst_field_datatype as ft ON q.field_type_id=ft.id
                LEFT JOIN knowyourapproval_options as o ON q.id=o.q_id AND o.is_active = 1
                LEFT JOIN knowyourapproval_questions as dq ON o.id=dq.depend_option_id AND dq.is_active = 1              
                LEFT JOIN mst_services as s ON o.service_id=s.id
                where q.is_active = 1 order by q.preference_order ASC";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $resultarray = [];

        foreach ($data as $item) {
            $q_id = $item['q_id'];

           
            if (isset($resultarray[$q_id])) {
               
                $resultarray[$q_id]['options'][] = [
                    'o_id' => $item['o_id'],
                    'options' => $item['options'],
                    'option_no' => $item['option_no'],
                    'dq_no' => $item['dq_no'],
                    'service_name' => $item['service_name']
                ];
            } else {
              
                $resultarray[$q_id] = [
                    'q_id' => $item['q_id'],
                    'questions' => $item['questions'],
                    'question_no' => $item['question_no'],
                    'fieldtype' => $item['fieldtype'],
                    'options' => []
                ];

           
                if ($item['o_id'] || $item['options']) {
                    $resultarray[$q_id]['options'][] = [
                        'o_id' => $item['o_id'],
                        'options' => $item['options'],
                        'option_no' => $item['option_no'],
                        'dq_no' => $item['dq_no'],
                        'service_name' => $item['service_name']
                    ];
                }
            }
        }


        $resultarray = array_values($resultarray);
        return $resultarray;
    }




    public function actionManageQuestion(){
         $token = Token::tokenGenerator(Yii::$app->user->id);  
         $data = $this->getkyadata();      
        if(isset($_POST['q_id']) && $_POST['q_id']){
            $model = KnowyourapprovalQuestions::findOne($_POST['q_id']);
            return $this->asJson([
                'status' => true,    
                'model' => $model,  
                'kyadata' => $data,
                'optionData' => $this->optiondata($model->id),   
                'serviceList' => \app\models\masters\Services::find()->where(['is_active'=>1])->all(),     
                'token'=>$token
            ]); 
        }
    }

    public function actionAddKyaOption(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        
        if(isset($_POST['option']) && $_POST['option'] && Helper::CheckNotEmptyCondition($_POST['option']) && $_POST['q_id']){
            $Qmodel = KnowyourapprovalQuestions::findOne($_POST['q_id']);
            $model = new KnowyourapprovalOptions;
            $model->options = $_POST['option'];
            $model->q_id = $_POST['q_id'];
            $model->preference_order = KnowyourapprovalOptions::getnextPreferenceorderno($model->q_id);            
            $model->drive_question_id = null;
            $model->scpm_id = null;
            if(Helper::CheckNotEmptyCondition($_POST['service_id'])){
                 $model->service_id = $_POST['service_id'];
            }            
            $model->created_by = Yii::$app->user->id;
            $model->updated_by = Yii::$app->user->id;
            $model->created_on = date('Y-m-d H:i:s');
            $model->updated_on = date('Y-m-d H:i:s');
            if(!$model->save()){
                return $this->asJson([
                    'status' => false,    
                    'message' => $model->errors,    
                    'token'=>$token
                ]);
                
            }   

            return $this->asJson([
                'status' => true,    
                'q_id' => $Qmodel->id,  
                'optionData' => $this->optiondata($Qmodel->id), 
                'kyadata' => $this->getkyadata(),   
                'token'=>$token
            ]); 

        }else{
             return $this->asJson([
                'status' => false,
                'message' => 'Parameter missing',
                
            ]);   
        }      
    }

    protected function optiondata($q_id){
        $sql = "SELECT o.id as o_id,o.preference_order as option_no, o.options, dq.preference_order as dq_no , s.service_name
                FROM knowyourapproval_options as o                 
                LEFT JOIN mst_services as s ON o.service_id=s.id
                LEFT JOIN knowyourapproval_questions as dq ON o.id=dq.depend_option_id AND dq.is_active = 1
                where o.is_active = 1 AND o.q_id = $q_id order by o.preference_order ASC
                ";

                $data = Yii::$app->db->createCommand($sql)->queryAll();
                return $data;
    }

    public function actionAddChildQuestion(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        
        if(isset($_POST['o_id']) && $_POST['o_id']){
           
            $model = KnowyourapprovalOptions::findOne($_POST['o_id']);
            

            return $this->asJson([
                'status' => true,    
                'selected_option_details' => ['text'=>'Add question to drive from option','o_id'=>$model->id,'option'=>$model->options],
                'kyadata' => $this->getkyadata(),
                'alldataType' => FieldDatatype::find()->all(),    
                'token'=>$token
            ]); 

        }else{
             return $this->asJson([
                'status' => false,
                'message' => 'Parameter missing',
                
            ]);   
        } 
    }
    
    // this is currently call by web
    public function actionGetcheckmappedquestion(){
        $o_id = isset($_GET['o_id']) ? $_GET['o_id'] : 0;
        $model = KnowyourapprovalOptions::findOne($o_id);
        if(@$model->drive_question_id){
            $cdq = KnowyourapprovalQuestions::findOne($model->drive_question_id);
            return $this->asJson([
                'status' => true,
                'q_no'=>$cdq->preference_order               
            ]); 
        }else{
            return $this->asJson([
                'status' => false,
                'message'=>'option not found or something went wrong'
                
            ]); 
        }       
    }

    public function actionEditOption(){
         $token = Token::tokenGenerator(Yii::$app->user->id);

       

        if(isset($_POST['o_id'])){
            $model = KnowyourapprovalOptions::findOne($_POST['o_id']);
            if($model){
                $Qmodel = KnowyourapprovalQuestions::findOne($model->q_id);
                return $this->asJson([
                    'status' => true,
                    'model'=>$model,
                    'Qmodel'=>$Qmodel,
                    'kyadata' => $this->getkyadata(), 
                    'optionData' => $this->optiondata($Qmodel->id),   
                    'serviceList' => \app\models\masters\Services::find()->all(),
                    'token' => $token          
                ]); 
            }else{
                 return $this->asJson([
                    'status' => false,
                    'message'=>'No data found'                
                ]); 
            }            
        }else{
            return $this->asJson([
                'status' => false,
                'message'=>'parameter missing'                
            ]); 
        }        
    }

    public function actionEditKyaOption(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        
        if(isset($_POST['o_id']) && $_POST['option'] && Helper::CheckNotEmptyCondition($_POST['o_id']) && $_POST['q_id']){
            $Qmodel = KnowyourapprovalQuestions::findOne($_POST['q_id']);
            $model = KnowyourapprovalOptions::findOne($_POST['o_id']);
            $model->options = $_POST['option'];
            $model->scpm_id = null;
            if(Helper::CheckNotEmptyCondition($_POST['service_id'])){
                 $model->service_id = $_POST['service_id'];
            }                      
            $model->updated_by = Yii::$app->user->id;           
            $model->updated_on = date('Y-m-d H:i:s');
            if(!$model->save()){
                return $this->asJson([
                    'status' => false,    
                    'message' => $model->errors,    
                    'token'=>$token
                ]);
                
            }   

            return $this->asJson([
                'status' => true,    
                'q_id' => $Qmodel->id,  
                'optionData' => $this->optiondata($Qmodel->id), 
                'kyadata' => $this->getkyadata(),   
                'token'=>$token
            ]); 

        }else{
             return $this->asJson([
                'status' => false,
                'message' => 'Parameter missing',
                
            ]);   
        }      
    }

}
