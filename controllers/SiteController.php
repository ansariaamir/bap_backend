<?php

namespace app\controllers;


use agielks\yii2\jwt\JwtBearerAuth;
// Use your own login form
use common\models\LoginForm;
use Yii;
use yii\base\InvalidConfigException;

use yii\web\Controller;
use yii\web\Response;
use app\models\transactions\IncentiveFields;
use app\models\transactions\IncentiveData;
use app\models\masters\OptionValue;
use app\models\masters\MstUserrole;
use app\models\masters\FieldDatatype;
 use Mpdf\Mpdf;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

class SiteController extends Controller
{
    public function actionIndex(){
        $this->layout = 'web';

        return $this->render('index');
    }

    public function actionIncentiveResult(){
        $this->layout = 'web';
        if(isset($_POST['_csrf'])){
            unset($_POST['_csrf']);

            $conditions = [];
            $params = [];

            foreach ($_POST as $key => $value) {
               $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(form_field_data, '$.\"$key\"')) = :value_$key";
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

        return $this->render('incentive_search',['grid_data'=>$grid_data]);

        }

        return $this->render('incentive_search',['grid_data'=>null]);
        
    }

    public function actionKya(){
         $this->layout = 'web';
        $sql = "SELECT q.id as q_id, q.questions, q.preference_order as question_no, o.id as o_id, o.options, o.preference_order as option_no, ft.type as fieldtype, q.depend_option_id
                FROM knowyourapproval_questions as q
                LEFT JOIN mst_field_datatype as ft ON q.field_type_id=ft.id
                LEFT JOIN knowyourapproval_options as o ON q.id=o.q_id AND o.is_active = 1                               
                where q.is_active = 1  order by q.preference_order ASC";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $resultarray = [];

        foreach ($data as $item) {
            $q_id = $item['q_id'];

           
            if (isset($resultarray[$q_id])) {
               
                $resultarray[$q_id]['options'][] = [
                    'o_id' => $item['o_id'],
                    'options' => $item['options'],
                    'option_no' => $item['option_no']
                   
                ];
            } else {
              
                $resultarray[$q_id] = [
                    'q_id' => $item['q_id'],
                    'questions' => $item['questions'],
                    'question_no' => $item['question_no'],
                    'fieldtype' => $item['fieldtype'],
                    'depend_option_id' => $item['depend_option_id'],
                    'options' => []
                ];

           
                if ($item['o_id'] || $item['options']) {
                    $resultarray[$q_id]['options'][] = [
                        'o_id' => $item['o_id'],
                        'options' => $item['options'],
                        'option_no' => $item['option_no']                       
                        
                    ];
                }
            }
        }


        $resultarray = array_values($resultarray);
       
        return $this->render('kya',['data'=>$resultarray]);
    }

    public function actionKyaSubmit(){
        $this->layout = 'web';
        if(isset($_POST)){
            unset($_POST['_csrf']);
            $selectedoptions = $_POST;
            if(is_array($selectedoptions) && !empty($selectedoptions)){
                $options_ids = [];
                foreach ($selectedoptions as $key => $value) {
                    if(is_array($value)){
                        foreach ($value as $val) {
                            $options_ids[] = $val;
                        }
                    }else{
                        $options_ids[] = $value;
                    }
                }               

                $oIds = implode(',', $options_ids);

                if($oIds){
                  $data =  Yii::$app->db->createCommand("SELECT s.*
                   FROM knowyourapproval_options as o 
                            INNER JOIN mst_services s ON o.service_id = s.id
                            where o.id IN ($oIds) ")->queryAll();
                 
                  return $this->render('kya_result',['data'=>$data]);
                }else{
                    Yii::$app->session->setFlash('warning','Sorry no data found');
                    $this->redirect(Yii::$app->request->referrer);
                }              

            }else{
                Yii::$app->session->setFlash('warning','Please select atleast one option to know your approval');
                $this->redirect(Yii::$app->request->referrer);
            }
        }

    }


    public function actionApplicationPrint(){
        $command = Yii::$app->db;
        // if(Yii::$app->user->id){
        //     $token = Token::tokenGenerator(Yii::$app->user->id);

                $model = \app\models\transactions\TApplicationSubmission::findOne(2);
                  if($model){
                    $field_data = (array) json_decode($model->form_field_data,true);
                    $print_data = [];
                    foreach ($field_data as $tab_mapping_id => $value) {

                        $tabDetail = \app\models\ServiceTabMapping::findOne($tab_mapping_id);

                        $sectionDetail = \app\models\ServiceFormTabSectionMapping::find()->where(['stm_id'=>$tabDetail->id])->orderBy('preference_order ASC')->all();

                        $section_array = [];
                        foreach ($sectionDetail as $skey => $svalue) {
                            $field_data = \app\models\ServiceFormTabSectionFormFieldsMapping::find()->where(['sftsm_id'=>$svalue->id])->orderBy('preference_order ASC')->all();

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
                        // $mpdf->SetWatermarkImage(Yii::$app->urlManager->baseUrl.'/img/logo.png',0.1,
                        //     '',
                        //     [10, 10]);
                       $mpdf->showWatermarkImage = false;
                        $mpdf->SetHTMLFooter('<hr><table width="100%"><tr><td>'.($model->scpm->service->service_name.'-'.@$model->scpm->dept->dept_name).'</td> 
                            <td style="text-align:right;">page no: {PAGENO}</td></tr></table>');

                         $mpdf->WriteHTML($this->renderPartial('pdf',[
                                            'title' => 'Application Print PDF',
                                            'data' => $print_data,
                                            'model'=>$model
                                        ]));


                                            $mpdf->Output();
                    
                       }

                       
    }

   
}
