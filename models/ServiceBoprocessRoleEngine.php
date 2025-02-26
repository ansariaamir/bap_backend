<?php

namespace app\models;

use Yii;
use app\models\masters\MstUserrole;
/**
 * This is the model class for table "service_boprocess_role_engine".
 *
 * @property int $id
 * @property int $scpm_id
 * @property int $role_id
 * @property int $level_stage_no
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 * @property int $is_active
  * @property int|null $maxday_fpa max days for process the application
 */
class ServiceBoprocessRoleEngine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_boprocess_role_engine';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scpm_id', 'role_id', 'created_on', 'created_by', 'updated_on', 'updated_by'], 'required'],
            [['scpm_id', 'role_id', 'created_by', 'updated_by', 'is_active', 'maxday_fpa','level_stage_no'], 'integer'],
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
            'scpm_id' => 'Scpm ID',
            'role_id' => 'Role ID',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
            'maxday_fpa' => 'Maxday Fpa',
        ];
    }

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScpm()
    {
        return $this->hasOne(ServiceConfigParameterMapping::class, ['id' => 'scpm_id']);
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

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    // public function getProle()
    // {
    //     return $this->hasOne(MstUserrole::class, ['id' => 'previous_role_id']);
    // }

     /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    // public function getNrole()
    // {
    //     return $this->hasOne(MstUserrole::class, ['id' => 'next_role_id']);
    // }

/*
*  This function is use on service config workflow ForBuilder
* param is selected role_id get 
*/
    public static function previousRole(){

    }

    public static function NextRole(){
        
    }
}
