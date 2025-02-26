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

class PdfController extends RestController{

	
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
                
                $print_data = [];

                $applicantData = TApplicationSubmission::makeCompleteArrayByUploadData($model->form_field_data);

                $print_data = [
                    'form_field_data'=>TApplicationSubmission::fflabels($applicantData['ff']),
                    'dms_data'=>TApplicationDms::getDetails($model->id)
                ];

                    // $field_data = (array) json_decode($model->form_field_data,true);
                    // $print_data = [];

                    // foreach ($field_data as $key => $value) {
                    //     $section_array = [];
                    //     foreach ($sectionDetail as $skey => $svalue) {
                    //         $field_data = ServiceFormTabSectionFormFieldsMapping::find()->where(['sftsm_id'=>$svalue->id])->orderBy('preference_order ASC')->all();

                    //         foreach ($field_data as $fkey => $fvalue) {
                                
                    //                 $is_array= is_array($value) ? true : false;

                    //                 if($is_array==true && @$fvalue->fdt->type=='addmore'){
                    //                     // get table header
                    //                     $zero_index_for_heading = $value[0];
                    //                     $thead = [];
                    //                     foreach ($zero_index_for_heading as $tk => $tv) {
                    //                         $mst_ff = $command->createCommand("SELECT form_field_name FROM mst_form_fields WHERE form_field_id='".$tv['form_field_code_id']."'")->queryOne();
                    //                         $thead[] = @$mst_ff['form_field_name'] ;
                    //                     }
                    //                     $field_value = [
                    //                         'thead' => $thead,
                    //                         'tbody'=>$value,
                    //                     ];
                    //                 }else{
                    //                     $field_value = $value;
                    //                 }

                    //                 $section_array[$svalue->sc->section][] = [
                    //                     'field_name'=>$fvalue->field_name,
                    //                     'field_is_array_value' => $is_array,
                    //                     'field_type' => @$fvalue->fdt->type,
                    //                     'field_value'=>$field_value
                    //                 ];
                                
                                 
                    //         }
                    //     }

                    //     $print_data[$tabDetail->tab_name] = $section_array;
                    // }


                        $mpdf = new Mpdf([
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 40,
                            'margin_bottom' => 15,
                            'margin_header' => 5,
                            'margin_footer' => 5
                        ]);
                        $mpdf->SetHTMLHeader('<table style="text-align:center;" width="100%">
                            <tr>
                                <td>
                                    <img src="' . Yii::$app->urlManager->baseUrl . '/img/emblem-motivation-black.png" alt="Logo" class="logo-img" style="height:120px; width: auto;">
                                </td>
                                <td>
                                    <h2>'.@$model->scpm->dept->dept_name.'</h2>
                                    <h4>'.$model->scpm->service->service_name.'</h4>
                                </td>
                                <td><br><span>App ID: <br><b>'.$model->id.'</br></span></td>
                            </tr>
                        </table>
                        <hr>');
                        $mpdf->SetWatermarkImage(Yii::$app->urlManager->baseUrl.'/img/emblem-motivation-black.png',0.2,
                            '',
                            [10, 10]);
                        $mpdf->showWatermarkImage = false;


// stamp code

$stampHtml = '

    <table style="border-collapse: collapse; padding: 2px; width: 200px; float: right;">
       
        <tr>
             <td style="text-align:center;">
                <span><img src="'.Yii::$app->urlManager->baseUrl.'/uploads/signature/sign.png" alt="sign" style="height:50px;"></span>   
            </td>
        </tr>
        <tr>
            <td style="color:blue; text-align:center;">
                <span style="font-size: 9;"><strong>DATE</strong> <span style="color:black;">'.date('d-m-Y').'</span></span>   
            </td>
        </tr>
        <tr>
            <td style="color:blue; text-align:center;">
                <span style="font-size: 7;"><strong>e-Anuddan</strong></span><br>
                <span style="font-size: 4;">Department Of Social Justice & Empowerment</span>  
            </td>
        </tr>
    </table>
';

// Write the stamp HTML to the PDF


//stamp code end

                        $mpdf->SetHTMLFooter($stampHtml.'<hr><table width="100%"><tr><td width="34%">Printed On: '.date('d-m-Y h:i a').'</td><td style="text-align:center;" width="33%">Printed By: '.Yii::$app->user->identity->name.'</td> 
                            <td style="text-align:right;" width="33%">page no: {PAGENO}</td></tr></table>');

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
                        
                        $mpdf->SetWatermarkImage(Yii::$app->urlManager->baseUrl.'/img/emblem-motivation-black.png',0.08,
                            40,
                            [105, 45]);
                        $mpdf->showWatermarkImage = true;
                       

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

                        $mpdf->Image(Yii::$app->urlManager->baseUrl.'/uploads/signature/sign.png', 220, 150, 50, 20, 'jpg', '', true, false);

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