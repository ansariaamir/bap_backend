<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_tab_type".
 *
 * @property int $id
 * @property string $tab_name
 * @property string $short_code
 */
class TabType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_tab_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tab_name', 'short_code'], 'required'],
            [['tab_name'], 'string', 'max' => 50],
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
            'tab_name' => 'Tab Name',
            'short_code' => 'Short Code',
        ];
    }
}
