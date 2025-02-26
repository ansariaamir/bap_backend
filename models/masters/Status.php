<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_status".
 *
 * @property int $id
 * @property string $status
 * @property string|null $short_code
 * @property int $is_active
 * @property string $created_on
 * @property string|null $updated_on
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_on'], 'required'],
            [['is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['status'], 'string', 'max' => 50],
            [['short_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'short_code' => 'Short Code',
            'is_active' => 'Is Active',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
        ];
    }
}
