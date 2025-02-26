<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_option_value".
 *
 * @property int $id
 * @property int|null $option_id mst option id
 * @property int|null $parent_option_value_id
 * @property string $name
 * @property string|null $name_hindi
 * @property string|null $code
 * @property int|null $preference_order
 * @property int $created_by
 * @property string $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 * @property int $is_active
 *
 * @property MstOption $option
 * @property OptionValue[] $optionValues
 * @property OptionValue $parentOptionValue
 * @property ServiceConfigParameterMapping[] $serviceConfigParameterMappings
 * @property ServiceConfigParameterMapping[] $serviceConfigParameterMappings0
 * @property ServiceConfigParameterMapping[] $serviceConfigParameterMappings1
 */
class OptionValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_option_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_id', 'parent_option_value_id', 'created_by', 'updated_by', 'is_active','preference_order'], 'integer'],
            [['name', 'created_by', 'created_on', 'is_active'], 'required'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'name_hindi'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 55],
            
            [['parent_option_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => OptionValue::class, 'targetAttribute' => ['parent_option_value_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_id' => 'Option ID',
            'parent_option_value_id' => 'Parent Option Value ID',
            'name' => 'Name',
            'name_hindi' => 'Name Hindi',
            'code' => 'Code',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'updated_by' => 'Updated By',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Option]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Option::class, ['id' => 'option_id']);
    }

    /**
     * Gets query for [[OptionValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOptionValues()
    {
        return $this->hasMany(OptionValue::class, ['parent_option_value_id' => 'id']);
    }

    /**
     * Gets query for [[ParentOptionValue]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParentOptionValue()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'parent_option_value_id']);
    }

    /**
     * Gets query for [[ServiceConfigParameterMappings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceConfigParameterMappings()
    {
        return $this->hasMany(ServiceConfigParameterMapping::class, ['entity_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceConfigParameterMappings0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceConfigParameterMappings0()
    {
        return $this->hasMany(ServiceConfigParameterMapping::class, ['country_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceConfigParameterMappings1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceConfigParameterMappings1()
    {
        return $this->hasMany(ServiceConfigParameterMapping::class, ['state_id' => 'id']);
    }

    public static function getallData($option_id){

        //return \yii\helpers\ArrayHelper::map();
        return self::find()->where(['option_id'=>$option_id])->all();
    }

    public static function getValueByID($id){
        if($id){
            $model = self::findOne($id);
            return $model->name;
        }else{
            return null;
        }
        
    }

    public static function getValueByIDs($ids){
        if($ids){
        $model = Yii::$app->db->createCommand("SELECT name FROM mst_option_value WHERE id IN ($ids)")->queryAll();
        return $model;
        }else{
            return null;
        }
    }

    public static function GetOptionValue($option_id=NULL,$parent_option_value_id=NULL){
        if($option_id==NULL){
            if($parent_option_value_id==NULL){
                $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1])->all();
            }else{
                $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$parent_option_value_id])->all();
            }            
        }else{
            if($parent_option_value_id==NULL){
                $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'option_id'=>$option_id])
                ->orderBy('preference_order ASC')
                ->all();
            }else{
                $data = \app\models\masters\OptionValue::find()->where(['is_active'=>1,'parent_option_value_id'=>$parent_option_value_id,'option_id'=>$option_id])->all();
            }  
        }

        return $data;
    }

}
