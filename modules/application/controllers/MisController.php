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
use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

class MisController extends RestController{

    
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

     
    public function actionSummaryLevelOne(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        $state_id = $district_id = $service_id = null;
        $state_condi = $district_condi = $service_condi = "";
        $from_date = $to_date = date('Y-m-d');
        if(isset($_POST['state_id']) && Helper::CheckNotEmptyCondition($_POST['state_id'])){
            $state_id = $_POST['state_id'];
            $state_condi = "AND a.state_id=$state_id";
        }


        if(isset($_POST['district_id']) && Helper::CheckNotEmptyCondition($_POST['district_id'])){
            $district_id = $_POST['district_id'];
            $district_condi = "AND a.district_id=$district_id";
        }

        if(isset($_POST['service_id']) && Helper::CheckNotEmptyCondition($_POST['service_id'])){
            $service_id = $_POST['service_id'];
            $service_condi = "AND s.id=$service_id";
        }

        if(isset($_POST['from_date']) && Helper::CheckNotEmptyCondition($_POST['from_date'])){
            $from_date = $_POST['from_date'];
        }

        if(isset($_POST['to_date']) && Helper::CheckNotEmptyCondition($_POST['to_date'])){
            $to_date = $_POST['to_date'];
        }

      
        $data = Yii::$app->db->createCommand("SELECT 
        SUM(IF(a.application_status = 'D', 1, 0)) AS D,
        SUM(IF(a.application_status = 'H', 1, 0)) AS H,
       SUM(IF(a.application_status IN ('P','F'), 1, 0)) AS P,
        SUM(IF(a.application_status = 'A', 1, 0)) AS A,
        SUM(IF(a.application_status = 'R', 1, 0)) AS R,
        s.service_name,
        s.id
    FROM t_application_submission a
    INNER JOIN service_config_parameter_mapping scpm ON a.scpm_id = scpm.id
    INNER JOIN mst_services s ON scpm.service_id = s.id
    WHERE date(a.updated_on) BETWEEN :from_date AND :to_date
    $state_condi  $district_condi  $service_condi 
    GROUP BY s.id, s.service_name")
        ->bindValue(':from_date', date('Y-m-d',strtotime($from_date)))
        ->bindValue(':to_date', date('Y-m-d',strtotime($to_date)))
        ->queryAll();

        return [
            'status'=>true,
            'message'=>'summary report level 1',
            'data'=>$data,
            'token'=>$token
        ];


    }
    
    public function actionSummaryLevelTwo(){
        $token = Token::tokenGenerator(Yii::$app->user->id);
        if(isset($_POST['from_date']) && Helper::CheckNotEmptyCondition($_POST['from_date'])){
            $from_date = $_POST['from_date'];
        }

        if(isset($_POST['to_date']) && Helper::CheckNotEmptyCondition($_POST['to_date'])){
            $to_date = $_POST['to_date'];
        }

        if(isset($_POST['service_id']) && Helper::CheckNotEmptyCondition($_POST['service_id'])){
            $service_id = $_POST['service_id'];
            $service_condi = "AND s.id=$service_id";
        }

        if(isset($_POST['status']) && Helper::CheckNotEmptyCondition($_POST['status'])){
            $status = $_POST['status'];
             if($status=='P'){
                $status_condition = " AND a.application_status IN ('P','F')";
            }else{
                $status_condition = "AND a.application_status='".$status."'";
            }
            
        }

        $data = Yii::$app->db->createCommand("SELECT 
            a.id as app_id, 
            CONCAT('EA-NAPDDR-000',a.id) as project_id,
            a.scpm_id as scpm_id,
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
            WHERE date(a.updated_on) BETWEEN :from_date AND :to_date
            $service_condi
            $status_condition
            ORDER BY a.id DESC
            ")
        ->bindValue(':from_date', date('Y-m-d',strtotime($from_date)))
        ->bindValue(':to_date', date('Y-m-d',strtotime($to_date)))
        ->queryAll();

        return [
            'status'=>true,
            'message'=>'summary report level 2',
            'data'=>$data,
            'token'=>$token
        ];


    }
    
}

?>