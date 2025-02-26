<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_services".
 *
 * @property int $id
 * @property string|null $service_name
 * @property string|null $service_desc
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 *
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
            [['created_by', 'updated_by','is_active'], 'integer'],
            [['service_desc'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
           
            [['service_name'], 'string', 'max' => 255],
          
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_name' => 'Service Name',
            'service_desc' => 'Service Desc',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
        ];
    }


    public static function getallData(){

        //return \yii\helpers\ArrayHelper::map();
        return self::find()->all();
    }

    public static function getServicenotMapped(){
        $service_ids = Yii::$app->db->createCommand("SELECT GROUP_CONCAT(service_id SEPARATOR ',') AS service_ids FROM service_config_parameter_mapping")->queryScalar();
        if($service_ids){
             $service_data = Yii::$app->db->createCommand("SELECT * FROM mst_services WHERE id NOT IN ($service_ids) AND is_active=1 ")->queryAll();
        }else{
             $service_data = Yii::$app->db->createCommand("SELECT * FROM mst_services WHERE is_active=1 ")->queryAll();
        }
       
        return $service_data;
    }

    
}
