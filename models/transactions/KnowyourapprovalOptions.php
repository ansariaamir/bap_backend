<?php

namespace app\models\transactions;

use Yii;


/**
 * This is the model class for table "knowyourapproval_options".
 *
 * @property int $id
 * @property string|null $options
 * @property int|null $q_id
 * @property int|null $drive_question_id wo question jo yeh option se open hoga
 * @property int|null $scpm_id   // this is service id configuration id with other details also fetch
 * @property int|null $service_id
 * @property int|null $preference_order
 * @property string|null $created_on
 * @property int|null $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int|null $is_active
 */
class KnowyourapprovalOptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'knowyourapproval_options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['q_id', 'drive_question_id', 'scpm_id','service_id', 'preference_order', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['options'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'options' => 'Options',
            'q_id' => 'Q ID',
            'drive_question_id' => 'Drive Question ID',
            'scpm_id' => 'SCPM ID',
            'service_id' => 'Service ID',
            'preference_order' => 'Preference Order',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }

     public static function getnextPreferenceorderno($q_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from knowyourapproval_options WHERE is_active=1 AND q_id=$q_id")->queryScalar();

        return $maxid;
    } 
}
