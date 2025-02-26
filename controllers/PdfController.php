<?php 
namespace app\controllers;

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
        
class PdfController extends Controller{

	/**
     * @inheritdoc
     */
    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();
    //     $behaviors['authenticator'] = [
    //         'class' => JwtHttpBearerAuth::class,
    //         'optional' => [
    //             'application-print'
    //         ],
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

     

	public function actionApplicationPrint(){
		$command = Yii::$app->db;
        // if(Yii::$app->user->id){
        //     $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['application_id'])){
                $model = TApplicationSubmission::findOne($_POST['application_id']);
                  if($model){
                    $field_data = (array) json_decode($model->form_field_data,true);
                    $print_data = [];
                    foreach ($field_data as $tab_mapping_id => $value) {

                        $tabDetail = ServiceTabMapping::findOne($tab_mapping_id);

                        $sectionDetail = ServiceFormTabSectionMapping::find()->where(['stm_id'=>$tabDetail->id])->orderBy('preference_order ASC')->all();

                        $section_array = [];
                        foreach ($sectionDetail as $skey => $svalue) {
                            $field_data = ServiceFormTabSectionFormFieldsMapping::find()->where(['sftsm_id'=>$svalue->id])->orderBy('preference_order ASC')->all();

                            foreach ($field_data as $fkey => $fvalue) {
                                if(array_key_exists($fvalue->ff->form_field_id, $value)){
                                    $is_array= is_array($value[$fvalue->ff->form_field_id]) ? true : false;

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
                                        $field_value = $value[$fvalue->ff->form_field_id];
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

                        $print_data[$tabDetail->tab_name] = $section_array;
                    }


                        $mpdf = new Mpdf([
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 40,
                            'margin_bottom' => 10,
                            'margin_header' => 5,
                            'margin_footer' => 5
                        ]);
                        $mpdf->SetHTMLHeader('<table style="text-align:center;" width="100%">
                            <tr>
                                <td>
                                  
                                </td>
                                <td>
                                    <h1>'.$model->scpm->service->service_name.'</h1>
                                    <h2>'.@$model->scpm->dept->dept_name.'</h2>
                                </td>
                                <td><br><span>App ID: <br><b>'.$model->id.'</br></span></td>
                            </tr>
                        </table>
                        <hr>');
                        // $mpdf->SetWatermarkImage(Yii::$app->urlManager->baseUrl.'/img/ey_logo.png',0.1,
                        //     '',
                        //     [10, 10]);
                        $mpdf->showWatermarkImage = false;
                        $mpdf->SetHTMLFooter('<hr><table width="100%"><tr><td>'.($model->scpm->service->service_name.'-'.@$model->scpm->dept->dept_name).'</td> 
                            <td style="text-align:right;">page no: {PAGENO}</td></tr></table>');

                         $mpdf->WriteHTML($this->renderPartial('application_print',[
                                            'title' => 'Application Print PDF',
                                            'data' => $print_data,
                                            'model'=>$model
                                        ]));


                       
                       $pdf = $mpdf->Output();
                        
                        return $this->asJson([
                            'status' => true,
                            'pdf_data'=>$pdf,
                            //'token' => $token                   
                        ]);
                    }else{
                         return $this->asJson([
                            'status' => false, 'message'=>'Application not found',
                            //'token' => $token,  
                        ]);
                    }          
            }else{
                return $this->asJson([
                    'status' => false, 'message'=>'Parameter missing',
                    //'token' => $token,  
                ]);
            }            
        // }else{
        //      return $this->asJson([
        //         'status' => false, 'message'=>'Session expired. Please login again'
        //     ]);
        // } 
	}


    public function actionApplicationCertificate(){
        $command = Yii::$app->db;
        if(Yii::$app->user->id){
            $token = Token::tokenGenerator(Yii::$app->user->id);
            if(isset($_POST['application_id'])){
                $model = TApplicationSubmission::findOne($_POST['application_id']);
                  if($model){
                    
                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4-L',
                            'orientation' => 'L',
                            'margin_left' => 5,
                            'margin_right' => 5,
                            'margin_top' => 5,
                            'margin_bottom' => 5
                        ]);
                        
                        // $mpdf->SetWatermarkImage(Yii::$app->urlManager->baseUrl.'/img/ey_logo.png',0.08,
                        //     40,
                        //     [80, 35]);
                        // $mpdf->showWatermarkImage = true;
                       

                         $mpdf->WriteHTML($this->renderPartial('application_certificate',[
                                            'title' => 'Application Certificate PDF',
                                            'model'=>$model
                                        ])); 
                       
                       
                        // QrCode 
                        $box_link = Url::base(true).'/application/pdfprint?app_id='.base64_encode($model->id);
                        $qrCode = new QrCode($box_link);
                        $qrCode->disableBorder();
                        $output = new Output\Mpdf();           
                        $output->output($qrCode,$mpdf,48,150,28);

                        $mpdf->setXY(48,180);
                        $mpdf->WriteHtml('Scan to Verify');

                        //$mpdf->Image(Yii::$app->urlManager->baseUrl.'/uploads/signature/sign.png', 220, 150, 50, 20, 'jpg', '', true, false);
                        //$mpdf->Image('/uploads/signature/sign.png', 220, 150, 50, 20, 'jpg', '', true, false);
                        $mpdf->setXY(230,180);
                        $mpdf->WriteHtml('<b>Approver</b>');

                        
                        // end code
                        $pdf = $mpdf->Output();
                        return $this->asJson([
                            'status' => true,
                            'pdf_data'=>$pdf,
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