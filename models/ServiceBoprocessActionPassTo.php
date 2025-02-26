<?php

namespace app\models;

use Yii;
use app\models\masters\MstUserrole;
/**
 * This is the model class for table "service_boprocess_action_pass_to".
 *
 * @property int $id
 * @property int $action_acess_id
 * @property int $role_id
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 * @property int $is_active
 */
class ServiceBoprocessActionPassTo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_boprocess_action_pass_to';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action_acess_id', 'created_on', 'created_by', 'updated_on', 'updated_by'], 'required'],
            [['action_acess_id','role_id', 'created_by', 'updated_by', 'is_active','passto_role_engine_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_acess_id' => 'Action Access',
            'passto_role_engine_id' => 'passto_role_engine_id',
            'role_id' => 'Role ID',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
           
        ];
    }

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionacess()
    {
        return $this->hasOne(ServiceBoprocessActionAcess::class, ['id' => 'action_acess_id']);
    }

     /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(MstUserrole::class, ['id' => 'role_id']);
    }

    public static function GetPassToDetail($id){
        return self::find()->where(['id'=>$id])->one();
    }
   
}
