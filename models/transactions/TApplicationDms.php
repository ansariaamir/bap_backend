<?php

namespace app\models\transactions;

use Yii;
use app\models\transactions\TApplicationSubmission;
use app\models\ServiceDmsMapping;
/**
 * This is the model class for table "t_application_dms".
 *
 * @property int $id
 * @property int $application_id
 * @property int $dms_mapping_id
 * @property string $file_url
 * @property int $dms_status
 * @property string $remark
 * @property string $created_on
 * @property string $updated_on
 */
class TApplicationDms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_application_dms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'dms_mapping_id', 'dms_status', 'created_on', 'updated_on','file_url'], 'required'],
            [['application_id', 'dms_mapping_id'], 'integer'],
            [['remark','dms_status','file_url'], 'string'],
            [['created_on', 'updated_on'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'dms_mapping_id' => 'Dms Mapping ID',
            'dms_status' => 'Dms Status',
            'file_url' => 'File URL',
            'remark' => 'Remark',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
        ];
    }

     public function getApp()
    {
        return $this->hasOne(TApplicationSubmission::class, ['id' => 'application_id']);
    }

    public function getDmsmapp()
    {
        return $this->hasOne(ServiceDmsMapping::class, ['id' => 'dms_mapping_id']);
    }

    public static function getFullStatus($status){
        $status_array = ['P'=>'Pending','V'=>'Verified','R'=>'Rejected'];
        return @$status_array[$status];
    }

/**  now this is use in application PDF
 * */
    public static function getDetails($application_id){
        $data = self::find()->where(['application_id'=>$application_id])->all();
        $final_array = [];
        foreach ($data as $key => $value) {
            $final_array[] = [
                'name' => $value->dmsmapp->doc_name,
                'remark' => $value->remark,
                'upload_on' => $value->created_on,
                'file_url' => $value->file_url,
                'status'=>self::getFullStatus($value->dms_status)
            ];
        }
        return $final_array;
    }
}
