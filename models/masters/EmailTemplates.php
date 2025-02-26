<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "email_templates".
 *
 * @property int $id
 * @property string $email_name
 * @property string $email_subject
 * @property string $email_body
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 * @property int $is_active
 */
class EmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_name', 'email_subject', 'email_body', 'created_on', 'created_by', 'updated_on', 'updated_by'], 'required'],
            [['email_body'], 'string'],
            [['created_on', 'updated_on'], 'safe'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['email_name'], 'string', 'max' => 100],
            [['email_subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_name' => 'Email Name',
            'email_subject' => 'Email Subject',
            'email_body' => 'Email Body',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }
}
