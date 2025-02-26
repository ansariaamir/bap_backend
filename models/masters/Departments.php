<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_departments".
 *
 * @property int $id
 * @property string $dept_name
 * @property string|null $dept_desc
 * @property string|null $type    M:- ministries , D:- Department
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 *
 */
class Departments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_name'], 'required'],
            [['dept_desc', 'type'], 'string'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['dept_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dept_name' => 'Dept Name',
            'dept_desc' => 'Dept Desc',
            'type' => 'Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
        ];
    }

    public static function getallData($type=null){

        //return \yii\helpers\ArrayHelper::map();
        if($type){
            return self::find()->where(['type'=>$type])->all();
        }else{
            return self::find()->all();
        }
        
    }
    
}
