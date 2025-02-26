<?php

namespace app\models\transactions;

use Yii;
use app\models\ServiceConfigParameterMapping;
use app\models\User;
use app\models\masters\OptionValue;
use app\models\masters\FormFields;
use app\models\masters\FieldDatatype;
use app\models\ServiceFormTabSectionFormFieldsMapping;
/**
 * This is the model class for table "t_application_submission".
 *
 * @property int $id
 * @property int $service_id
 * @property int $scpm_id service config parameter mapping id
 * @property int $district_id
 * @property int $state_id
 * @property string $form_field_data
 * @property int $application_status
 * @property int $sso_user_id
 * @property int $where_app_is_role_id
 * @property string $created_on
 * @property string $updated_on
 * @property int $is_active
 * 
 * @property ServiceConfigParameterMapping $scpm
 */
class TApplicationSubmission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_application_submission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'scpm_id', 'form_field_data', 'application_status', 'sso_user_id', 'created_on'], 'required'],
            [['service_id', 'scpm_id', 'sso_user_id', 'is_active','where_app_is_role_id','district_id','state_id'], 'integer'],
            [['form_field_data','application_status'], 'string'],
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
            'service_id' => 'Service ID',
            'scpm_id' => 'Scpm ID',
            'form_field_data' => 'Form Field Data',
            'application_status' => 'Application Status',
            'sso_user_id' => 'Sso User ID',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScpm()
    {
        return $this->hasOne(ServiceConfigParameterMapping::class, ['id' => 'scpm_id']);
    }

     /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSsouser()
    {
        return $this->hasOne(User::class, ['id' => 'sso_user_id']);
    }

    public function getState()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'state_id']);
    }
     public function getDistrict()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'district_id']);
    }


    public static function BouserActions_applicationStatus($action){

        $actions_array = [
            'revert'=>['application_status'=>'H'],
            'forward'=>['application_status'=>'F'],
            'approve'=>['application_status'=>'A'],
            'reject'=>['application_status'=>'R']
        ];

        if(array_key_exists($action, $actions_array)){
            return $actions_array[$action];
        }else{
            return NULL;
        }
    }

    // this action is for status label
    public static function applicationStatus($application_status){

        $actions_array = [
            'D'=>['application_status_label'=>'Draft'],
            'P'=>['application_status_label'=>'Pending'],
            'H'=>['application_status_label'=>'Reverted'],
            'F'=>['application_status_label'=>'Forwarded'],
            'A'=>['application_status_label'=>'Approved'],
            'R'=>['application_status_label'=>'Rejected']
        ];

         if(array_key_exists($application_status, $actions_array)){
            return $actions_array[$application_status];
        }else{
            return NULL;
        }
    }

    public static function FindEachValue($fieldData, $fieldCode){
         //$field_data = (array) json_decode($fieldData,true);
         foreach ($fieldData as $k => $value) {
            if ($k === $fieldCode) {
                return $value;
            }

            if (is_array($value)) {
                $result = self::FindEachValue($value, $fieldCode);
                if ($result !== null) {
                    return $result;
                }
            }
        }
        return null;
    }

/*
*  this function to return you the seperation of array depends on key code prefix
*/
public static function makeCompleteArrayByUploadData($fields_data) {
        $fieldCodess = ['ff', 'dms', 'dec'];
        $fieldsData = json_decode($fields_data, true);

        $result = [];

        foreach ($fieldCodess as $prefix) {
            $result[$prefix] = [];
        }

        self::processArray($result, $fieldCodess, $fieldsData);

        return $result;
    }

    static function addToResult(&$result, $fieldCodess, $key, $value) {
        foreach ($fieldCodess as $prefix) {
            if (strpos($key, $prefix) === 0) {
                $result[$prefix][$key] = $value;
                return;
            }
        }
    }

    static function processArray(&$result, $fieldCodess, $array, $prefix = null) {
        foreach ($array as $key => $value) {
            if (is_array($value) && !self::isAssociativeArray($value)) {
                // Handle array of objects/arrays (like Add Family Detail)
                foreach ($value as $subValue) {
                    if (is_array($subValue)) {
                        $nestedResult = [];
                        self::processArray($nestedResult, $fieldCodess, $subValue, $prefix);
                        foreach ($nestedResult as $nestedKey => $nestedValue) {
                            $result[$nestedKey][$key][] = $nestedValue;
                        }
                    }
                }
            } elseif (is_array($value)) {
                // Handle associative arrays
                $nestedResult = [];
                self::processArray($nestedResult, $fieldCodess, $value, $prefix);
                $result[$prefix ?: $key] = array_merge_recursive($result[$prefix ?: $key], $nestedResult);
            } else {
                self::addToResult($result, $fieldCodess, $key, $value);
            }
        }
    }

    static function isAssociativeArray(array $array) {
        if (array() === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }


/*
*  this function return array of fields_name by replace field_code 
*/
public static function fflabels($formFieldsDataOnly){
    $newArray = [];
    $dynamicCheckValuesArray = FieldDatatype::GetOptionsfieldtype();
    foreach ($formFieldsDataOnly as $key => $value) {
        // Check if the key starts with "ff"
        if (strpos($key, 'ff') === 0) {
            $find_label = FormFields::find()->where(['form_field_id'=>$key])->one();
            $sftsffm = ServiceFormTabSectionFormFieldsMapping::find()->where(['ff_id'=>$find_label->id])->one();
            $newKey = $sftsffm->field_name;
        } else {
            $newKey = $key;
        }
        
        if (is_array($value)) {
            // Handle nested arrays recursively
            if (!empty($value) && array_keys($value) === range(0, count($value) - 1)) {
                // Handle indexed array
                $newArray[$newKey] = self::fflabels($value);
            } else {
                // Handle associative array
                $newArray[$newKey] = self::fflabels($value);
            }
        } else {
            if(in_array($sftsffm->fdt->type, $dynamicCheckValuesArray)){
                $OVModel = OptionValue::findOne($value);
                if($OVModel){
                    $newArray[$newKey] = $OVModel->name;
                }else{
                    $newArray[$newKey] = $value;
                }
            }else{
                $newArray[$newKey] = $value;
            }            
        }
    }
    
    return $newArray;
}

}
