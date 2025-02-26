<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_field_datatype".
 *
 * @property int $id
 * @property string $type
 */
class FieldDatatype extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_field_datatype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    public static function GetOptionsfieldtype(){
        return ['select', 'multiselect', 'checkbox', 'radio'];
    }
}
