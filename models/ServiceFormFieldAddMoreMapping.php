<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_form_field_add_more_mapping".
 *
 * @property int $id
 * @property int $add_more_field_id for now add more btn id set
 * @property int $form_field_id other fields which is take for multiapl records
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int $is_active
 *
 * @property ServiceFormTabSectionFormFieldsMapping $addMoreField
 * @property ServiceFormTabSectionFormFieldsMapping $formField
 */
class ServiceFormFieldAddMoreMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_form_field_add_more_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['add_more_field_id', 'form_field_id', 'created_on', 'created_by'], 'required'],
            [['add_more_field_id', 'form_field_id', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['add_more_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceFormTabSectionFormFieldsMapping::class, 'targetAttribute' => ['add_more_field_id' => 'id']],
            [['form_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceFormTabSectionFormFieldsMapping::class, 'targetAttribute' => ['form_field_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'add_more_field_id' => 'Add More Field ID',
            'form_field_id' => 'Form Field ID',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[AddMoreField]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddMoreField()
    {
        return $this->hasOne(ServiceFormTabSectionFormFieldsMapping::class, ['id' => 'add_more_field_id']);
    }

    /**
     * Gets query for [[FormField]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormField()
    {
        return $this->hasOne(ServiceFormTabSectionFormFieldsMapping::class, ['id' => 'form_field_id']);
    }
}
