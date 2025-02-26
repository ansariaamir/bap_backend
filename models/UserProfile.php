<?php

namespace app\models;

use Yii;
use app\models\masters\Departments;
use app\models\masters\MstUserrole;
use app\models\masters\OptionValue;
/**
 * This is the model class for table "mst_userrole".
 *
 * @property int $id
 * @property string $user_id
 * @property string $role_id
 * @property string $dept_id
 * @property int|null $state_id
 * @property int|null $district_id
 * @property int|null $gender
 * @property string|null $dob
 * @property string|null $full_address
 * @property string|null $is_default
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'dept_id', 'created_at','created_by'], 'required'],
            [['created_at','updated_at','deleted_at','dob','full_address'], 'safe'],
            [['user_id','role_id','dept_id','state_id','district_id','is_default','created_by','updated_by','gender'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user_id',
            'role_id' => 'role_id',
            'dept_id' => 'dept_id',
            'state_id' => 'state_id',
            'district_id' => 'district_id',
            'is_default' => 'is_default',
            'created_at' => 'created_at',
            'created_by' => 'created_by',
            'updated_at' => 'updated_at',
            'updated_by' => 'updated_by',
            'deleted_at' => 'deleted_at'
        ];
    }

     public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
     public function getRole()
    {
        return $this->hasOne(MstUserrole::class, ['id' => 'role_id']);
    }
     public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['id' => 'dept_id']);
    }
    
     public function getState()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'state_id']);
    }
     public function getDistrict()
    {
        return $this->hasOne(OptionValue::class, ['id' => 'district_id']);
    }


}
