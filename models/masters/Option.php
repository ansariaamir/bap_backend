<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_option".
 *
 * @property int $id
 * @property string $name
 * @property int $created_by
 * @property string $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 * @property int $is_active
 *
 * @property MstOptionValue[] $mstOptionValues
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_by', 'created_on'], 'required'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'updated_by' => 'Updated By',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[MstOptionValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMstOptionValues()
    {
        return $this->hasMany(MstOptionValue::class, ['option_id' => 'id']);
    }
}
