<?php

namespace app\models\transactions;

use Yii;
use app\models\User;
use app\models\masters\MstUserrole;
/**
 * This is the model class for table "t_application_log".
 *
 * @property int $id
 * @property int $application_id
 * @property int $user_id
 * @property int $role_id
 * @property int $application_status
 * @property string $comment
 * @property string $description
 * @property string $created_on
 */
class TApplicationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_application_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'user_id', 'application_status', 'comment', 'created_on'], 'required'],
            [['application_id', 'user_id','role_id'], 'integer'],
            [['comment', 'description','application_status'], 'string'],
            [['created_on'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'user_id' => 'User ID',
            'application_status' => 'Application Status',
            'comment' => 'Comment',
            'description' => 'Description',
            'created_on' => 'Created On',
        ];
    }

     /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getRole()
    {
        return $this->hasOne(MstUserrole::class, ['id' => 'role_id']);
    }

    public static function getTimeDifference($startDate, $endDate) {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);

        return [
            'years' => $interval->y,
            'months' => $interval->m,
            'days' => $interval->d,
            'hours' => $interval->h,
            'minutes' => $interval->i,
        ];
    }


    public static function GetCalculateDays($startDate,$endDate){
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        $secondsPerDay = 60 * 60 * 24;
        $daysDifference = ($endTimestamp - $startTimestamp) / $secondsPerDay;

        return ceil($daysDifference);
    }
    
}
