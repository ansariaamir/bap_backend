<?php

namespace app\models;

use Yii;
use app\models\masters\TabType;
/**
 * This is the model class for table "service_form_tab_mapping".
 *
 * @property int $id
 * @property int $scpm_id service config parameter mapping id
 * @property int $tab_type_id
 * @property string $tab_name like Applicant form etc
 * @property int $preference_order order number which tab or form will come first or last
 * @property int $created_by
 * @property string $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 * @property int $is_active
 *
 * @property ServiceConfigParameterMapping $scpm
 * @property ServiceFormTabSectionMapping[] $serviceFormTabSectionMappings
 * @property MstTabType $tabType
 */
class ServiceTabMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_tab_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scpm_id', 'tab_type_id', 'tab_name', 'created_by', 'created_on'], 'required'],
            [['scpm_id', 'tab_type_id', 'preference_order', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['tab_name'], 'string', 'max' => 255],
            [['scpm_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceConfigParameterMapping::class, 'targetAttribute' => ['scpm_id' => 'id']],
            [['tab_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TabType::class, 'targetAttribute' => ['tab_type_id' => 'id']],
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
            'tab_type_id' => 'Tab Type ID',
            'tab_name' => 'Tab Name',
            'preference_order' => 'Preference Order',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'updated_by' => 'Updated By',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
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

    /**
     * Gets query for [[ServiceFormTabSectionMappings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFormTabSectionMappings()
    {
        return $this->hasMany(ServiceFormTabSectionMapping::class, ['stm_id' => 'id']);
    }

    /**
     * Gets query for [[TabType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTabType()
    {
        return $this->hasOne(TabType::class, ['id' => 'tab_type_id']);
    }

    
    public static function getnextPreferenceorderno($scpm_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_tab_mapping WHERE scpm_id=$scpm_id AND is_active=1")->queryScalar();

        return $maxid;
    } 

// get that detail which can be active
    public static function getnexttab($scpm_id, $current_tab_id){
        if($current_tab_id==0){
            $currentPreference = 0;
        }else{
            $model = self::findOne($current_tab_id);
            $currentPreference = $model->preference_order;
        }
        $nextTab = self::find() 
            ->where(['>', 'preference_order', $currentPreference])
            ->andWhere(['is_active'=>1])
            ->andWhere(['scpm_id'=>$scpm_id])
            ->orderBy('preference_order ASC')
            ->one();
        return $nextTab;            
    } 
}
