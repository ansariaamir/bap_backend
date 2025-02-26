<?php

namespace app\models\transactions;

use Yii;
use app\models\ServiceConfigParameterMapping;
use app\models\User;
use app\models\masters\Option;
use app\models\masters\FieldDatatype;

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
 * 
 * @property FieldDatatype $fdt
 * @property FieldDatatype $fdt
 */
class IncentiveFields extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incentive_fields';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['field_name', 'type_id'], 'required'],
            [['field_name','placeholder'], 'string'],
            [['is_required', 'option_id', 'type_id', 'is_active','show_for_search','created_by','updated_by','created_on', 'updated_on','search_label'], 'safe'],
        ];
    }

    public function getFdt()
    {
        return $this->hasOne(FieldDatatype::class, ['id' => 'type_id']);
    }

     public function getOption()
    {
        return $this->hasOne(Option::class, ['id' => 'option_id']);
    }
   
}
