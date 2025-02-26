<?php 
namespace app\controllers;

use app\models\Token;
use app\models\Helper;
use agielks\yii2\jwt\JwtBearerAuth;
use yii\filters\Cors;
use Yii;
use app\controllers\MyController;
use yii\web\Response;
use app\models\masters\Declarations;
use app\models\masters\Departments;
use app\models\masters\Document;
use app\models\masters\Services;
use app\models\masters\FormFields;
use app\models\masters\SectionCategory;
use app\models\masters\Option;
use app\models\masters\OptionValue;
use app\models\masters\EmailTemplates;



class MastersController extends MyController{

	 // public function behaviors()
  //   {
  //       $behaviors = parent::behaviors();
  //       $behaviors['authenticator'] = [
  //           'class' => JwtHttpBearerAuth::class,
  //           'optional' => [
               
  //           ],
  //       ];
  //       return $behaviors;
  //   }
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

            if(isset($_POST['action_for'])){
                if(isset($_POST['keywords']) && $_POST['keywords']!=""){
                    $keywords_search = true;
                }else{
                    $keywords_search = false;
                }
                switch ($_POST['action_for']) {
                    case 'department':
                        $msg = "Departments Data";
                        
                        $condi = $keywords_search==true ? "WHERE dept_name Like '%".$_POST['keywords']."%' " : "";
                        $sql = "SELECT * FROM mst_departments $condi ORDER BY id DESC";
                        break;
                    case 'service':
                        $msg = "Service Data";
                        $condi = $keywords_search==true ? "WHERE service_name Like '%".$_POST['keywords']."%' " : "";
                        $sql = "SELECT * FROM mst_services $condi ORDER BY id DESC";
                        break;  
                    case 'declaration':
                        $msg = "Declaration Data";
                        $sql = "SELECT * FROM mst_declarations ORDER BY id DESC";
                        break; 

                    case 'document':
                        $msg = "Document Data";
                        $sql = "SELECT * FROM mst_document ORDER BY id DESC";
                        break; 
                    case 'sectioncategory':
                        $msg = "Form Fields Data";
                        $sql = "SELECT * FROM mst_section_category ORDER BY id DESC";
                        break; 
                    case 'options':
                        $msg = "Form Fields Data";
                        $condi = $keywords_search==true ? "WHERE name Like '%".$_POST['keywords']."%' " : "";
                        

                        $sql = "SELECT * FROM mst_option $condi  ORDER BY id DESC";
                        break; 
                    case 'optionsvalue':
                        $msg = "Form Fields Data";
                        $option_id = isset($_POST['option_id']) ? $_POST['option_id'] : '';
                        if($option_id=='all' || $option_id==""){
                            $option_condition  = "";
                        }else{
                            $option_condition = "WHERE ov.option_id=".$option_id;
                        }
                        $sql = "SELECT ov.*, o.name as o_name, pov.name as pov_name FROM mst_option_value as ov
                        INNER JOIN mst_option o ON ov.option_id=o.id
                        LEFT JOIN mst_option_value pov ON ov.parent_option_value_id=pov.id
                        $option_condition 
                        ";
                        break;                                 
                    case 'formfields':
                        $msg = "Form Fields Data";
                        $sql = "SELECT * FROM mst_form_fields ORDER BY id DESC";
                        break;                
                    
                    default:
                        $msg = 'Wrong action for given. please check';
                        $sql = NULL;
                        break;
                }

                if($sql){
                    $records = Yii::$app->db->createCommand($sql)->queryAll();
                }
                return $this->asJson([
                    'status' => true,
                    'message' => $msg,
                    'records' => isset($records) ? $records : NULL,
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

    public function actionCreateUpdate(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['action_for'])){

                switch ($_POST['action_for']) {
                    case 'department':
                        $msg = "Departments Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = Departments::findOne($_POST['id']);
                        }else{
                            $model = new Departments;
                            $model->created_at = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                        }
                        
                        $model->dept_name = $_POST['name'];
                        $model->dept_desc = $_POST['desc'];
                        $model->type = $_POST['type'];                         
                        $model->save();
                       
                        break;
                    case 'service':
                        $msg = "Service Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = Services::findOne($_POST['id']);
                        }else{
                            $model = new Services;
                            $model->created_at = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                        }
                        
                        $model->service_name = $_POST['name'];
                        $model->service_desc = $_POST['desc'];                         
                        $model->save();
                       
                        break;   

                    case 'declaration':
                        $msg = "Declaration Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = Declarations::findOne($_POST['id']);
                        }else{
                            $model = new Declarations;
                            $model->created_at = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                        }
                        
                        $model->declaration_name = $_POST['name'];
                        $model->declaration_text = $_POST['desc'];                     
                        $model->save();
                       
                        break;        
                    case 'document':
                        $msg = "Document Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = Document::findOne($_POST['id']);
                        }else{
                            $model = new Document;
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                        }
                        
                        $model->doc_name = $_POST['name'];
                        $model->doc_desc = $_POST['desc'];                         
                        $model->save();
                       
                        break;

                    case 'formfields':
                        $msg = "Form Fields Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = FormFields::findOne($_POST['id']);
                            $model->updated_by = Yii::$app->user->id;
                            $model->updated_on = date('Y-m-d H:i:s');
                        }else{
                            $model = new FormFields;
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                            $model->form_field_id = FormFields::getformfieldid();
                        }
                                                
                        $model->form_field_name = $_POST['name'];                       
                        $model->save();                       
                        break;  
                    case 'sectioncategory':
                        $msg = "Section Category Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = SectionCategory::findOne($_POST['id']);
                            $model->updated_by = Yii::$app->user->id;
                            $model->updated_on = date('Y-m-d H:i:s');
                        }else{
                            $model = new SectionCategory;
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                            
                        }
                                                
                        $model->section = $_POST['name'];                       
                        $model->save();                       
                        break;
                    case 'options':
                        $msg = "Option Data generated";
                        if(isset($_POST['id']) && $_POST['id']){
                            $model = Option::findOne($_POST['id']);
                            $model->updated_by = Yii::$app->user->id;
                            $model->updated_on = date('Y-m-d H:i:s');
                        }else{
                            $model = new Option;
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                            
                        }
                                                
                        $model->name = $_POST['name'];                       
                        $model->save();                       
                        break; 
                    case 'optionsvalue':
                        $msg = "OptionValue Data generated";

                        if(isset($_POST['id']) && $_POST['id']){
                            $model = OptionValue::findOne($_POST['id']);
                            $model->updated_by = Yii::$app->user->id;
                            $model->updated_on = date('Y-m-d H:i:s');
                        }else{
                            $model = new OptionValue;
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->user->id;
                            
                        }
                        $model->option_id = $_POST['option_id'];      
                        $model->name = $_POST['name']; 
                        $model->is_active = 1;              
                        if(!$model->save()){
                            return $model->errors;
                        }                       
                        break;        
                              
                    
                    default:
                        $msg = 'Wrong action for given. please check';
                        break;
                }

                
                return $this->asJson([
                    'status' => true,
                    'message' => $msg,
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

    public function actionGetDetails(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);

            if(isset($_POST['action_for'])){
                $id = $_POST['id'];
                switch ($_POST['action_for']) {
                    case 'department':
                        $msg = "Departments Data";
                        $sql = "SELECT * FROM mst_departments where id=$id";
                        break;
                    case 'service':
                        $msg = "Departments Data";
                        $sql = "SELECT * FROM mst_services where id=$id";
                        break;       
                    case 'declaration':
                        $msg = "Departments Data";
                        $sql = "SELECT * FROM mst_declarations where id=$id";
                        break;     
                    case 'document':
                        $msg = "Departments Data";
                        $sql = "SELECT * FROM mst_document where id=$id";
                        break;    
                    case 'sectioncategory':
                        $msg = "Section Category Data";
                        $sql = "SELECT * FROM mst_section_category where id=$id";
                        break; 
                    case 'options':
                        $msg = "Options Data";
                        $sql = "SELECT * FROM mst_option where id=$id";
                        break; 
                    case 'optionsvalue':
                        $msg = "Option Value Data";
                        $sql = "SELECT ov.*, o.name as o_name FROM mst_option_value as ov
                        INNER JOIN mst_option o ON ov.option_id=o.id
                        where ov.id=$id";
                        break;                                 
                    case 'formfields':
                        $msg = "Form Fields Data";
                        $sql = "SELECT * FROM mst_form_fields where id=$id";
                        break; 
                    default:
                        $msg = 'Wrong action for given. please check';
                        $sql = NULL;
                        break;
                }

                if($sql){
                    $records = Yii::$app->db->createCommand($sql)->queryOne();
                }
                return $this->asJson([
                    'status' => true,
                    'message' => $msg,
                    'records' => isset($records) ? $records : NULL,
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


    public function actionCreateProcedure(){
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['desc'])){
            $sql = "DELIMITER //
            CREATE PROCEDURE activeusers(
                IN comining_status INTEGER(1),
                IN role_id INTEGER(11)
            )
            BEGIN
            IF role_id IS NOT NULL THEN
                SELECT * FROM employees WHERE status=comining_status AND role_id= role_id ;
            ELSE
             SELECT * FROM employees WHERE status=comining_status
            END //
            DELIMITER ;";

            Yii::$app->db->createCommand($sql)->execute();
            return $this->asJson([
                    'status' => true,
                    'message' => 'Procedure has been created in DB',                    
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


public function actionGetMessageingData(){
     if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['messaging_type'])){
            $data = [];
            switch ($_POST['messaging_type']) {
                case 'email':
                    $sql = "SELECT * FROM email_templates where is_active=1";
                    $data = Yii::$app->db->createCommand($sql)->queryAll();
                    break;
                case 'whatsapp':
                    $data = [];
                    break;
                case 'sms':
                    $data = [];
                    break;        
                
                default:
                    $data = [];
                    break;
            }

            
            return $this->asJson([
                    'status' => true,
                    'data' => $data,                    
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

    public function actionAddUpdateEmail(){
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['name']) && $_POST['name'] && $_POST['subject'] && $_POST['body']){
                $model = new EmailTemplates;    
                if(isset($_POST['e_id']) && $_POST['e_id']){
                    $model = EmailTemplates::findOne($_POST['e_id']);
                }

                if($model->isNewRecord){
                    $model->created_on = date('Y-m-d H:i:s');
                    $model->created_by = Yii::$app->user->id;
                }
                
                $model->email_name = $_POST['name'];
                $model->email_subject = $_POST['subject'];
                $model->email_body = $_POST['body'];                
                $model->updated_on = date('Y-m-d H:i:s');
                $model->updated_by = Yii::$app->user->id;
                $model->save();

                $sql = "SELECT * FROM email_templates where is_active=1";
                $data = Yii::$app->db->createCommand($sql)->queryAll();

                return $this->asJson([
                    'status' => true,
                    'data' => $data,                    
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
    public function actionEditEmail(){
     if(Yii::$app->user->id){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['e_id']) && $_POST['e_id']){
            $sql = "SELECT * FROM email_templates where is_active=1 AND id=:e_id";
                    $command = Yii::$app->db->createCommand($sql);
                    $command = $command->bindValue(':e_id',$_POST['e_id']);
                   $data = $command->queryOne();
            

            
            return $this->asJson([
                    'status' => true,
                    'data' => $data,                    
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


    public function actionGetoptiondetails(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['option_id'])){

if(isset($_POST['keywords']) && $_POST['keywords']!=""){
    $keywords_search = true;
}else{
    $keywords_search = false;
}



            $optionModel = Option::findOne($_POST['option_id']);
            if($optionModel){
                $queryvalues = OptionValue::find()->where(['option_id'=>$optionModel->id,'is_active'=>1]);

if($keywords_search==true){
    $queryvalues->andWhere(['like', 'name', $_POST['keywords']]) ;
}
 

                
                $valueModel = null;
                if(isset($_POST['id']) && ($_POST['id']!=null || $_POST['id'] != 'null')){
                    $valueModel = OptionValue::findOne($_POST['id']);
                }

                return $this->asJson([
                    'status' => true,
                    'model' => $optionModel,
                    'values' => $queryvalues->orderBy('id DESC')->all(),
                    'valueModel'=>$valueModel,
                    'message' => 'Option found',
                    'token' => $token,      
                    
                ]);
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'Option not found',
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

    public function actionDeleteOptionValue(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['id']) && Helper::CheckNotEmptyCondition($_POST['id'])){
            $model = OptionValue::findOne($_POST['id']);
            if($model){
                $model->is_active = 0;
                $model->updated_by = Yii::$app->user->id;
                $model->updated_on = date("Y-m-d H:i:s");
                $model->save();
                return $this->asJson([
                    'status' => true,
                    'message' => 'Record deleted',
                    'token' => $token,      
                    
                ]);
            }else{
                return $this->asJson([
                    'status' => false,
                    'message' => 'No Data Found',
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
}



?>