<?php

namespace app\models;

use Yii;
use app\models\masters\SectionCategory;
/**
 * This is the model class for table "service_form_tab_section_mapping".
 *
 * @property int $id
 * @property int $stm_id service form tab mapping
 * @property int $sc_id mst section category id
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int $preference_order
 * @property int $is_active
 *
 * @property SectionCategory $sc
 * @property ServiceFormTabSectionFormFieldsMapping[] $serviceFormTabSectionFormFieldsMappings
 * @property ServiceTabMapping $stm
 */
class ServiceFormTabSectionMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_form_tab_section_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stm_id', 'sc_id', 'created_on', 'created_by'], 'required'],
            [['stm_id', 'sc_id', 'created_by', 'updated_by', 'is_active','preference_order'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['stm_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceTabMapping::class, 'targetAttribute' => ['stm_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => SectionCategory::class, 'targetAttribute' => ['sc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stm_id' => 'Stm ID',
            'sc_id' => 'Sc ID',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(SectionCategory::class, ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ServiceFormTabSectionFormFieldsMappings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFormTabSectionFormFieldsMappings()
    {
        return $this->hasMany(ServiceFormTabSectionFormFieldsMapping::class, ['sftsm_id' => 'id']);
    }

    /**
     * Gets query for [[Stm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStm()
    {
        return $this->hasOne(ServiceTabMapping::class, ['id' => 'stm_id']);
    }

    public static function getnextPreferenceorderno($stm_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_form_tab_section_mapping WHERE stm_id=$stm_id AND is_active=1")->queryScalar();

        return $maxid;
    } 

    public static function getallSectionOnForm($stm_id){
        $data = self::find()->where(['stm_id'=>$stm_id,'is_active'=>1])->all();
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = ['id'=>$value['id'],'section_name'=>$value->sc->section,'preference_order'=>$value['preference_order']];
        }
        return $result;
    }
}
