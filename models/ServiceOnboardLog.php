<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_onboard_log".
 *
 * @property int $id
 * @property int $scpm_id
 * @property string $tab_code
 * @property string $tab_name
 * @property int $current_step
 * @property int $next_step
 * @property string $created_on
 * @property int $created_by
 */
class ServiceOnboardLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_onboard_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scpm_id', 'tab_code', 'tab_name', 'current_step', 'next_step', 'created_on', 'created_by'], 'required'],
            [['scpm_id', 'current_step', 'next_step', 'created_by'], 'integer'],
            [['created_on'], 'safe'],
            [['tab_code', 'tab_name'], 'string', 'max' => 50],
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
            'tab_code' => 'Tab Code',
            'tab_name' => 'Tab Name',
            'current_step' => 'Current Step',
            'next_step' => 'Next Step',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
        ];
    }
}
