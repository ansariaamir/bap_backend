<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_section_category".
 *
 * @property int $id
 * @property string $section
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int $is_active
 */
class SectionCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_section_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['section', 'created_on', 'created_by'], 'required'],
            [['created_on', 'updated_on'], 'safe'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['section'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Section',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }
}
