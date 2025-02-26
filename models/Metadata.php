<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "metadata".
 *
 * @property int $id
 * @property string $input_text
 * @property string $conversion_in
 * @property string $response_data
 * @property string $audio_file_path
 * @property string $created_on
 * @property string $response_status
 * @property string $is_finally_convert
 */
class Metadata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metadata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['input_text','conversion_in','gender'], 'required'],
            [['input_text'], 'string'],
            [['response_data','created_on','response_status'], 'safe'],
            [['conversion_in','gender'], 'string', 'max' => 20],
            [['audio_file_path'], 'string', 'max' => 200],
            [['is_finally_convert'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'input_text' => 'Input Text',
            'conversion_in' => 'Target Language',
            'gender' => 'Voice of Gender',
            'response_data' => 'Response Data',
            'audio_file_path' => 'Audio File Path',
            'created_on' => 'created_on'
        ];
    }

    public function beforeValidate(){
        $this->created_on =  $this->created_on ? date('Y-m-d H:i:s',strtotime($this->created_on)) : "";
         return parent::beforeValidate();
    }

    public function afterFind(){
        $this->created_on = $this->created_on ? date('d-m-Y h:i a',strtotime($this->created_on)) : ""; 
        return parent::afterFind();
    }

    
}
