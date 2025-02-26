<?php

namespace app\models;

use Yii;
use app\models\masters\FormFields;
use app\models\masters\FieldDatatype;
/**
 * This is the model class for table "service_boprocess_action_pass_to".
 *
 * @property int $id
 * @property int $role_engine_id
 * @property int $field_datatype_id
 * @property int $ff_id
 * @property string $field_name
 * @property int $is_required
 * @property int $preference_order
 * @property string $placeholder
 * @property string $is_visible_for_role_engine_id
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 */
class ServiceBoprocessFormFieldsMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_boprocess_form_fields_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_engine_id','field_name'], 'required'],
            [['role_engine_id','ff_id', 'created_by', 'updated_by', 'field_datatype_id','is_required','preference_order'], 'integer'],
            [['created_on', 'updated_on','field_name','placeholder','is_visible_for_role_engine_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_engine_id' => 'Role Engine',
            'ff_id' => 'FF',
            'field_name' => 'field_name',
            'field_datatype_id'=>'field_datatype_id',
            'is_required'=>'is_required',
            'placeholder'=>'placeholder',
            'preference_order'=>'preference_order',
            'is_visible_for_role_engine_id'=>'is_visible_for_role_engine_id',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            
           
        ];
    }

     public function getSbpre()
    {
        return $this->hasOne(ServiceBoprocessRoleEngine::class, ['id' => 'role_engine_id']);
    }

    /**
     * Gets query for [[Ff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFf()
    {
        return $this->hasOne(FormFields::class, ['id' => 'ff_id']);
    }

    /**
     * Gets query for [[Fdt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFdt()
    {
        return $this->hasOne(FieldDatatype::class, ['id' => 'field_datatype_id']);
    }

    public static function getnextPreferenceorderno($re_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_boprocess_form_fields_mapping WHERE role_engine_id=$re_id")->queryScalar();

        return $maxid;
    } 

    public static function getallFormFieldsOnSection($re_id){
        $data = self::find()->where(['role_engine_id'=>$re_id])->all();
        $result = [];
        foreach ($data as $key => $value) {
            
            $vrIds = explode(',', $value['is_visible_for_role_engine_id']);
            $is_vf_re = [];
            foreach ($vrIds as $role_engine_id) {
                $vRole = ServiceBoprocessRoleEngine::find()->where(['id'=>$role_engine_id])->one();
                $is_vf_re[] = $vRole->role->role_name_label;
            }

            $result[] = [
                'id'=>$value['id'],
                'field_id'=>$value->ff->form_field_id,
                'field_name'=>$value->field_name,
                'field_type'=>$value->fdt->type,
                'is_required'=>$value['is_required'],
                'placeholder'=>$value['placeholder'],
                'is_visible_for_role_engine_id' => implode(',', $is_vf_re),
                'preference_order'=>$value['preference_order']
          ];
        }
        return $result;
    }

   
}
