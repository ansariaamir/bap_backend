<?php

namespace app\models\transactions;

use Yii;

use app\models\masters\Departments;
use app\models\masters\MstUserrole;
use app\models\transaction\TApplicationSubmission;
/**
 * This is the model class for table "t_application_forward_departments".
 *
 * @property int $id
 * @property int $application_id
 * @property int $department_id
 * @property int $state_id
 * @property int $district_id
 * @property int $role_id
 * @property string $status
 * @property string $created_on
 * @property int $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 */
class TApplicationForwardDepartments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_application_forward_departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
            [['application_id', 'department_id', 'state_id','district_id','role_id','created_by','updated_by'], 'integer'],
            [['created_on','updated_on'], 'safe'],
            [['status'], 'string', 'max' => 20],
           
        ];
    }

    

    public function getAppdata()
    {
        return $this->hasOne(TApplicationSubmission::class, ['id' => 'application_id']);
    }

   public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'department_id']);
    }

    public function getRole()
    {
        return $this->hasOne(MstUserrole::class, ['id' => 'role_id']);
    }
}
