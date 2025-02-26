<?php
namespace app\models\transactions;

use Yii;


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
 * @property FieldDatatype $fdt
 * @property FieldDatatype $fdt
 */
class IncentiveData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incentive_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           
            [['is_active','form_field_data','created_by','updated_by','created_on', 'updated_on'], 'safe'],
        ];
    }

    
   
}
