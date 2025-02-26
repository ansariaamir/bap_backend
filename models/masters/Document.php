<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_document".
 *
 * @property int $id
 * @property string $doc_name
 * @property string|null $doc_desc
 * @property string $created_on
 * @property int|null $created_by
 * @property string|null $updated_on
 * @property int $updated_by
 * @property int $is_active
 */
class Document extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doc_name', 'created_on', 'created_on'], 'required'],
            [['doc_desc'], 'string'],
            [['updated_on','updated_by'], 'safe'],
            [['created_by', 'updated_by', 'is_active'], 'integer'],
            [['doc_name'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doc_name' => 'Doc Name',
            'doc_desc' => 'Doc Desc',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }
}
