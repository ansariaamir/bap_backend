<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_form_fields".
 *
 * @property int $id
 * @property string $form_field_id     //autogenereated code like ff1
 * @property string $form_field_name
 * @property string $created_on
 * @property int $created_by
 * @property int|null $updated_by
 * @property string|null $updated_on
 * @property int $is_active
 */
class FormFields extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_form_fields';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_field_id', 'form_field_name', 'created_on', 'created_by'], 'required'],
            [['created_on', 'updated_on'], 'safe'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['form_field_id'], 'string', 'max' => 50],
            [['form_field_name'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_field_id' => 'Form Field ID',
            'form_field_name' => 'Form Field Name',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
        ];
    }

    public static function getformfieldid(){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(id),0)+1 as maxid from mst_form_fields")->queryScalar();

        return 'ff'.$maxid;
    } 
}
