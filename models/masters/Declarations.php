<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_declarations".
 *
 * @property int $id
 * @property string $declaration_name
 * @property string $declaration_text
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 */
class Declarations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_declarations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['declaration_name', 'declaration_text'], 'required'],
            [['declaration_text'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['declaration_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'declaration_name' => 'Declaration Name',
            'declaration_text' => 'Declaration Text',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
        ];
    }
}
