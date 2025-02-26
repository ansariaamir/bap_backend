<?php

namespace app\models;

use Yii;
use app\models\masters\FormFields;
use app\models\masters\FieldDatatype;
use app\models\masters\Option;
use app\models\ServiceFormFieldAddMoreMapping;
/**
 * This is the model class for table "service_form_tab_section_form_fields_mapping".
 *
 * @property int $id
 * @property int $sftsm_id service tab section mapping
 * @property int $ff_id mst form field id
 * @property string $field_name
 * @property int $field_datatype_id mst field datatype id  dropdown, text etc
 * @property int $is_required
 * @property string|null $placeholder
 * @property string|null $option_master_id // this value is use when dropdown or any other option type select
 * @property string|null $static_options this is use only for one or two options
 * @property int|null $depends_on_sftsffm_id this is the same table primary key
 * @property int $is_add_more_field
 * @property string|null $preference_order order number which tab or form will come first or last
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int $is_active
 *
 * @property FormFields $ff
 * @property FieldDatatype $fdt
 * @property Option $option
 * @property ServiceFormFieldAddMoreMapping[] $serviceFormFieldAddMoreMappings
 * @property ServiceFormFieldAddMoreMapping[] $serviceFormFieldAddMoreMappings0
 * @property ServiceFormTabSectionMapping $sftsm
 */
class ServiceFormTabSectionFormFieldsMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_form_tab_section_form_fields_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sftsm_id', 'ff_id', 'field_name', 'field_datatype_id', 'is_required', 'created_on', 'created_by'], 'required'],
            [['sftsm_id', 'ff_id', 'field_datatype_id', 'is_required', 'created_by', 'updated_by', 'is_active','option_master_id','depends_on_sftsffm_id','is_add_more_field'], 'integer'],
            [['static_options'], 'string'],
            [['created_on', 'updated_on','preference_order'], 'safe'],
            [['field_name'], 'string', 'max' => 500],
            [['placeholder'], 'string', 'max' => 500],
            [['sftsm_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceFormTabSectionMapping::class, 'targetAttribute' => ['sftsm_id' => 'id']],
            [['ff_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormFields::class, 'targetAttribute' => ['ff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sftsm_id' => 'Sftsm ID',
            'ff_id' => 'Ff ID',
            'field_name' => 'Field Name',
            'field_datatype_id' => 'Field Datatype ID',
            'is_required' => 'Is Required',
            'placeholder' => 'Placeholder',
            'option_master_id' => 'Options Master',
            'static_options' => 'Static Options',
            'preference_order' => 'Preference Order',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
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

    

    /**
     * Gets query for [[ServiceFormFieldAddMoreMappings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFormFieldAddMoreMappings()
    {
        return $this->hasMany(ServiceFormFieldAddMoreMapping::class, ['add_more_field_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceFormFieldAddMoreMappings0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFormFieldAddMoreMappings0()
    {
        return $this->hasMany(ServiceFormFieldAddMoreMapping::class, ['form_field_id' => 'id']);
    }

    /**
     * Gets query for [[Sftsm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSftsm()
    {
        return $this->hasOne(ServiceFormTabSectionMapping::class, ['id' => 'sftsm_id']);
    }


// this relation is apply on same table 
    public function getSftsffm()
    {
        return $this->hasOne(ServiceFormTabSectionFormFieldsMapping::class, ['id' => 'depends_on_sftsffm_id']);
    }

    public function getOption()
    {
        return $this->hasOne(Option::class, ['id' => 'option_master_id']);
    }

    public static function getnextPreferenceorderno($s_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_form_tab_section_form_fields_mapping WHERE sftsm_id=$s_id AND is_active=1")->queryScalar();

        return $maxid;
    } 

    public static function getallFormFieldsOnSection($s_id){
        $data = self::find()->where(['sftsm_id'=>$s_id,'is_active'=>1])->all();
        $result = [];
        foreach ($data as $key => $value) {
            if($value->is_add_more_field){
                $model = ServiceFormFieldAddMoreMapping::find()->where(['is_active'=>1,'form_field_id'=>$value['id']])->One();
                $parent_field_name = $model ? $model->addMoreField->field_name : "";
            }else{
                $parent_field_name = "";
            }
            $result[] = [
                'id'=>$value['id'],
                'field_id'=>$value->ff->form_field_id,
                'field_name'=>$value->field_name,
                'field_type'=>$value->fdt->type,
                'is_required'=>$value['is_required'],
                'placeholder'=>$value['placeholder'],
                'option_master_id'=>$value['option_master_id'],
                'option_master_name' => ($value->option_master_id ? $value->option->name : NULL),
                //'static_options'=>$value['static_options'],
                'depends_on_sftsffm_id'=>$value->depends_on_sftsffm_id,
                'depends_on_label'=>($value->depends_on_sftsffm_id ? ('Depends On <br><b>'.$value->sftsffm->field_name.' - '.$value->ff->form_field_id.'</b>') : ''),
                'this_is_add_more_btn' => ($value->field_datatype_id==9 ? 'Yes' : 'No'),
                'is_add_more_field' => $value->is_add_more_field,
                'parent_field_name' => $parent_field_name,
                'preference_order'=>$value['preference_order']
          ];
        }
        return $result;
    }
    
public static function getalladdmorebtnsOnSection($s_id){
    $data = self::find()->where(['sftsm_id'=>$s_id,'is_active'=>1,'field_datatype_id'=>9])->all();
    // field_datatype_id == 9  means it a add more btn field type
    return $data;
}
    
public static function parent_fields_dynamic_options($s_id){
    $data = self::find()
    ->where(['sftsm_id'=>$s_id,'is_active'=>1])
    ->andWhere(['is not', 'option_master_id', null])
    ->all();
    return $data;
}


}
