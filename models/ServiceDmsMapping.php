<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\transactions\TApplicationDms;
use app\models\transactions\TDmsVerification;
/**
 * This is the model class for table "service_dms_mapping".
 *
 * @property int $id
 * @property int $scpm_id
 * @property int $doc_id
 * @property string $doc_name
 * @property int $is_required
 * @property int $preference_order
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 * @property int $is_active
 * @property string $allow_file_type enter comma seperated extentions like png, jpeg, pdf etc
 * @property int $allow_file_size integer only and size in unit MB
 *
 * @property ServiceConfigParameterMapping $scpm
 */
class ServiceDmsMapping extends \yii\db\ActiveRecord
{

    public $dms_code;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_dms_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scpm_id', 'doc_id', 'doc_name', 'is_required', 'preference_order', 'created_on', 'created_by', 'allow_file_type', 'allow_file_size'], 'required'],
            [['scpm_id', 'doc_id', 'is_required', 'preference_order', 'created_by', 'updated_by', 'is_active', 'allow_file_size'], 'integer'],
            [['created_on', 'updated_on','dms_code'], 'safe'],
            [['doc_name'], 'string', 'max' => 500],
            [['allow_file_type'], 'string', 'max' => 200],
            [['scpm_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceConfigParameterMapping::class, 'targetAttribute' => ['scpm_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scpm_id' => 'Scpm ID',
            'doc_id' => 'Doc ID',
            'doc_name' => 'Doc Name',
            'is_required' => 'Is Required',
            'preference_order' => 'Preference Order',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
            'allow_file_type' => 'Allow File Type',
            'allow_file_size' => 'Allow File Size',
        ];
    }

    /**
     * Gets query for [[Scpm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScpm()
    {
        return $this->hasOne(ServiceConfigParameterMapping::class, ['id' => 'scpm_id']);
    }

     public static function getnextPreferenceorderno($scpm_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_dms_mapping WHERE scpm_id=$scpm_id AND is_active=1")->queryScalar();

        return $maxid;
    } 

    public function afterFind()
    {
        parent::afterFind();        
        $this->dms_code = 'dms' . $this->doc_id;
    }


    // this function call from outer controller servicemanage index action
    public static function getDMS_mapped_withUploaded_data($scpm_id,$application_id){
        $dms_mapping = self::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->orderBy('preference_order ASC')->all();
        $dms_data = [];
         foreach ($dms_mapping as $key => $value) {
            $t_app_dms_id = $remark = $file_url = $status = NULL;
            if($application_id!=NULL){
             $TApplicationDms = TApplicationDms::find()->where(['application_id'=>$application_id,'dms_mapping_id'=>$value->id])->One(); 
             if($TApplicationDms){
                $t_app_dms_id = $TApplicationDms->id;
                $remark=$TApplicationDms->remark;
                $file_url = Url::base(true).'/'.$TApplicationDms->file_url;
                $boUserDmsData = TDmsVerification::find()->where(['t_app_dms_id'=>$TApplicationDms->id])->all();
                $boUserOutput = [];
                    foreach ($boUserDmsData as $val) {
                        $boUserOutput[] = [
                            'remark'=>$val->comment,
                            'status' => TApplicationDms::getFullStatus($val->status),
                            'user'=>$val->user->name,
                            'action_on' => $val->created_on
                        ];
                    }                
                }
                $status = TApplicationDms::getFullStatus($TApplicationDms->dms_status);
            }

            $dms_data[] = [
                'dms_mapping_id'=>$value->id,
                'doc_id' => $value->doc_id,
                 'doc_name' => $value->doc_name,
                 'is_required' => $value->is_required,
                 'allow_file_type' => $value->allow_file_type,
                 'allow_file_size' => $value->allow_file_size,
                 't_app_dms_id' => $t_app_dms_id,
                 'uploaded_remark' => $remark,
                 'file_url' => $file_url,
                 'status'=>$status,
                 'boUserOutput' => isset($boUserOutput) ? $boUserOutput : null   
            ]; 
         }

         return $dms_data;

    }

    public static function getDMS_mapped_withUploaded_data_forp($scpm_id,$stm_id,$category_name,$application_id){
        $dms_mapping = self::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->orderBy('preference_order ASC')->all();
        $dms_data = [];
         foreach ($dms_mapping as $key => $value) {
            $t_app_dms_id = $remark = $file_url = NULL;
            if($application_id!=NULL){
             $TApplicationDms = TApplicationDms::find()->where(['application_id'=>$application_id,'dms_mapping_id'=>$value->id,'dms_status'=>'P'])->One(); 
             if($TApplicationDms){
                $t_app_dms_id = $TApplicationDms->id;
                $remark=$TApplicationDms->remark;
                $file_url = Url::base(true).'/'.$TApplicationDms->file_url;
                
             }
            }

            $allow_file_type = explode(',', $value->allow_file_type);
            $allowFileSize = [];
            foreach ($allow_file_type as $key => $val) {
                if($val=='pdf'){
                    $allowFileSize[] = 'application/pdf';
                }else{
                   $allowFileSize[] = 'application/png';
                   $allowFileSize[] = 'application/jpg';
                   $allowFileSize[] = 'application/jpeg';
                }               
            }

            $dms_data[] = [
                'id'=>$value->id,
                'id_desc'=>'dms_mapping_id',
                'key' => $value->dms_code,
                'field_name' => $value->doc_name,
                'type'=>'file',
                'disabled'=>false,
                'required' => $value->is_required ? true : false,
                'placeholder' => 'upload document of '.$value->doc_name,          
                'allow_file_type' => $allowFileSize,
                'allow_file_size' => $value->allow_file_size,
                't_app_dms_id' => $t_app_dms_id,
                'uploaded_remark' => $remark,
                'file_url' => $file_url   
            ]; 
         }

$data[] = [
                    'tab_id' => $stm_id,
                    'category'=>$category_name,
                    'fields'=>$dms_data
                ];

         
         return $data;
         

    }


}
