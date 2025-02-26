<?php

namespace app\models\transactions;

use Yii;

/**
 * This is the model class for table "knowyourapproval_questions".
 *
 * @property int $id
 * @property string $questions
 * @property int $is_multiple 0 is signle option select , 1 is for multipal option select
 * @property int $is_required 0 not required, 1 is for required
 * @property int|null $depend_option_id
 * @property int|null $preference_order
 * @property string|null $created_on
 * @property int|null $created_by
 * @property string|null $updated_on
 * @property int|null $updated_by
 * @property int|null $is_active
 */
class KnowyourapprovalQuestions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'knowyourapproval_questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['questions'], 'required'],
            [['questions'], 'string'],
            [['is_multiple', 'is_required', 'depend_option_id', 'preference_order', 'created_by', 'updated_by', 'is_active','field_type_id'], 'integer'],
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
            'questions' => 'Questions',
            'is_multiple' => 'Is Multiple',
            'is_required' => 'Is Required',
            'depend_option_id' => 'Depend Option ID',
            'preference_order' => 'Preference Order',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'updated_on' => 'Updated On',
            'updated_by' => 'Updated By',
            'is_active' => 'Is Active',
        ];
    }

    public static function getnextPreferenceorderno(){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from knowyourapproval_questions WHERE is_active=1")->queryScalar();

        return $maxid;
    } 
}
