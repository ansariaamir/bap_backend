<?php

namespace app\models;

use Yii;
use app\models\masters\OptionValue;
use app\models\ServiceBoprocessRoleEngine;
/**
 * This is the model class for table "service_boprocess_action_acess".
 *
 * @property int $id
 * @property int|null $role_engine_id
 * @property string $action_access revert, Forward, Approve, Reject
 * @property string $action_access_label Reverted, Forwarded, Approved, Rejected
 * @property string $created_on
 * @property int $created_by
 * @property string $updated_on
 * @property int $updated_by
 * @property int $is_active
 * 
 * @property ServiceBoprocessRoleEngine $sbpre
 */
class ServiceBoprocessActionAcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_boprocess_action_acess';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_engine_id','action_access', 'action_access_label', 'created_on', 'created_by', 'updated_on', 'updated_by'], 'required'],
            [['role_engine_id', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['action_access_label','action_access'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_access' => 'Action Access',
            'role_engine_id' => 'Role Engine ID',
            'action_access_label' => 'Action Access Label',
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
    public function getSbpre()
    {
        return $this->hasOne(ServiceBoprocessRoleEngine::class, ['id' => 'role_engine_id']);
    }

    // public function getActionaccess()
    // {
    //     return $this->hasOne(OptionValue::class, ['id' => 'action_access']);
    // }

/*
*  this function is for dropdown action acess directly pass in response in configurationcontroller actionGetInnerPagesDetails
* this function is not in use now on 21-7-2024 when working on processdata
*/
    public static function getallActions($re_id=null){
        if($re_id){
            $for_which_role = ServiceBoprocessRoleEngine::find()->where(['id'=>$re_id,'is_active'=>1])->one();
            if($for_which_role){
                $scpm_id = $for_which_role['scpm_id'];
                $check_dept_role = Yii::$app->db->createCommand("SELECT re.id as re_id, mur.id as role_id, re.role_id as re_role_id, mur.role_type, mur.role_name, mur.role_name_label 
                    FROM service_boprocess_role_engine re 
                    INNER JOIN mst_userrole mur ON re.role_id = mur.id 
                    WHERE mur.role_type='BO' AND mur.role_name = 'department' AND re.is_active=1 AND re.scpm_id = $scpm_id
                    ")->queryOne();
                if($check_dept_role){
                     return Yii::$app->params['bo_users_actions_when_dept'];
                }else{
                     return Yii::$app->params['bo_users_actions'];
                }               
            }           
        }  
        return false;     
    }


   public static function getallroleToPass($re_id=null){
        if($re_id){
            $for_which_role = ServiceBoprocessRoleEngine::find()->where(['id'=>$re_id,'is_active'=>1])->one();
            if($for_which_role){
                $scpm_id = $for_which_role['scpm_id'];
                $role_id = $for_which_role['role_id'];
                $remain_role = Yii::$app->db->createCommand("SELECT re.id as re_id, mur.id as role_id, re.role_id as re_role_id, mur.role_type, mur.role_name, mur.role_name_label 
                    FROM service_boprocess_role_engine re 
                    INNER JOIN mst_userrole mur ON re.role_id = mur.id
                    WHERE re.is_active=1 AND re.scpm_id = $scpm_id AND re.role_id NOT IN ($role_id)
                    ")->queryAll();
                return     $remain_role;          
            }           
        }  
        return false; 
   }

   public static function GetActionDetail($id){
    return self::find()->where(['id'=>$id])->one();
   }
}
