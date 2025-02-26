<?php

namespace app\models;

use Yii;
use app\models\masters\Departments;
use app\models\masters\Services;
use app\models\masters\OptionValue;


/**
 * This is the model class for table "service_config_parameter_mapping".
 *
 * @property int $id
 * @property int $dept_id
 * @property int $service_id
 * @property int $entity_id option value id
 * @property int $country_id option value id
 * @property int $state_id option value id
 * @property int $is_payment_service if 1 then yes for payment else no payment form show
 * @property int $is_dms
 * @property int $is_declaration
 * @property int $is_mc master checklist
 * @property int $is_signature_detail
 * @property int $is_certificate_generate
 * @property int $total_application_days // this is for BO users to process the applicantion under this 
 * @property int $is_workflow_done
 * @property int $is_deemed_approved
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int $is_active
 * @property string|null $remark
 *
 * @property OptionValue $country
 * @property User $createdBy
 * @property Departments $dept
 * @property OptionValue $entity
 * @property Services $service
 * @property OptionValue $state
 * @property Users $updatedBy
 */
class ServiceConfigParameterMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_config_parameter_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_id', 'service_id', 'entity_id', 'country_id', 'created_on', 'created_by'], 'required'],
            [['dept_id', 'service_id', 'entity_id', 'country_id', 'state_id', 'is_payment_service', 'is_dms', 'is_declaration', 'is_mc', 'is_signature_detail', 'is_certificate_generate','is_workflow_done', 'created_by', 'updated_by', 'is_active','total_application_days','is_deemed_approved'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['remark'], 'string'],
            [['dept_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['dept_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::class, 'targetAttribute' => ['service_id' => 'id']],
            [['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => OptionValue::class, 'targetAttribute' => ['entity_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => OptionValue::class, 'targetAttribute' => ['country_id' => 'id']],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => OptionValue::class, 'targetAttribute' => ['state_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dept_id' => 'Dept ID',
            'service_id' => 'Service ID',
            'entity_id' => 'Entity ID',
            'country_id' => 'Country ID',
            'state_id' => 'State ID',
            'is_payment_service' => 'Is Payment Service',
            'is_dms' => 'Is Dms',
            'is_declaration' => 'Is Declaration',
            'is_mc' => 'Is Mc',
            'is_signature_detail' => 'Is Signature Detail',
            'is_certificate_generate' => 'Is Certificate Generate',
            'total_application_days' => 'Total Application Days For BO User Processing',
            'is_workflow_done' => 'Is Workflow Done',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
            'remark' => 'Remark',
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'country_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDept()
    {
        return $this->hasOne(Departments::class, ['id' => 'dept_id']);
    }

    /**
     * Gets query for [[Entity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'entity_id']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::class, ['id' => 'service_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'state_id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
