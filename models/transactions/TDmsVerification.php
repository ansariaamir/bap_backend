<?php

namespace app\models\transactions;

use Yii;
use app\models\transactions\TApplicationDms;
use app\models\User;
/**
 * This is the model class for table "t_dms_verification".
 *
 * @property int $id
 * @property int $t_app_dms_id
 * @property int $user_id
 * @property int $status
 * @property string $comment
 * @property string $created_on
 */
class TDmsVerification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 't_dms_verification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['t_app_dms_id', 'user_id', 'status', 'comment', 'created_on'], 'required'],
            [['t_app_dms_id', 'user_id'], 'integer'],
            [['comment'], 'string'],
            [['created_on','status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            't_app_dms_id' => 'T App Dms ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_on' => 'Created On',
        ];
    }

    public function getAppdms()
    {
        return $this->hasOne(TApplicationDms::class, ['id' => 't_app_dms_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
