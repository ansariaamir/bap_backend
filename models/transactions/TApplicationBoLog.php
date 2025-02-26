<?php

namespace app\models\transactions;

use Yii;

/**
 * This is the model class for table "t_application_bo_log".
 *
 * @property int $id
 * @property int $application_log_id
 * @property string $form_field_data
 * @property string $action_taken
 * @property int|null $application_forward_departments_id
 * @property int $bo_user_id
 * @property int $bo_role_id
 * @property string $created_on
 */
class TApplicationBoLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_application_bo_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_log_id', 'form_field_data', 'action_taken', 'bo_user_id', 'bo_role_id', 'created_on'], 'required'],
            [['application_log_id', 'bo_user_id', 'bo_role_id','application_forward_departments_id'], 'integer'],
            [['created_on','translation_text','form_field_data'], 'safe'],
            [['action_taken'], 'string', 'max' => 20],
            [['audio_file_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_log_id' => 'Application Log ID',
            'form_field_data' => 'form_field_data',
            'action_taken' => 'Action Taken',
            'application_forward_departments_id' => 'application_forward_departments_id',
            'bo_user_id' => 'Bo User ID',
            'bo_role_id' => 'Bo Role ID',
            'created_on' => 'Created On',
        ];
    }
}
