<?php

namespace app\models;

use Yii;
use app\models\masters\Declarations;
/**
 * This is the model class for table "service_declaration_mapping".
 *
 * @property int $id
 * @property int $scpm_id
 * @property int $declaration_id
 * @property int $preference_order
 * @property int $created_by
 * @property string $created_on
 * @property int $updated_by
 * @property string $updated_on
 * @property int $is_active
 *
 * @property ServiceConfigParameterMapping $scpm
 * @property Declarations $declaration
 */
class ServiceDeclarationMapping extends \yii\db\ActiveRecord
{
    public $dec_code;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_declaration_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scpm_id', 'declaration_id', 'preference_order', 'created_by', 'created_on'], 'required'],
            [['scpm_id', 'declaration_id', 'preference_order', 'created_by', 'updated_by', 'is_active'], 'integer'],
            [['created_on', 'updated_on','dec_code'], 'safe'],
            [['scpm_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceConfigParameterMapping::class, 'targetAttribute' => ['scpm_id' => 'id']],
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
            'declaration_id' => 'Declaration ID',
            'preference_order' => 'Preference Order',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'updated_by' => 'Updated By',
            'updated_on' => 'Updated On',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Scpm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScpm()
    {
        return $this->hasOne(ServiceConfigParameterMapping::class, ['id' => 'scpm_id']);
    }


    public function getDeclaration()
    {
        return $this->hasOne(Declarations::class, ['id' => 'declaration_id']);
    }

     public function afterFind()
    {
        parent::afterFind();        
        $this->dec_code = 'dec' . $this->declaration_id;
    }

    public static function getnextPreferenceorderno($scpm_id){
        $maxid = Yii::$app->db->createCommand("SELECT IFNULL (max(preference_order),0)+1 as maxid from service_declaration_mapping WHERE scpm_id=$scpm_id AND is_active=1")->queryScalar();

        return $maxid;
    } 


    public static function getDec_mapped_data($scpm_id){
        $dec_mapping = self::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->orderBy('preference_order ASC')->all();
        $dec_mapping_data = [];
        foreach ($dec_mapping as $key => $value) {
            $dec_mapping_data[] = ['dec_mapping_id'=>$value->id,'declaration'=>$value->declaration->declaration_text];
        }
         return $dec_mapping_data;

    }

    public static function getDec_mapped_data_forp($scpm_id, $stm_id, $category_name){
        $dec_mapping = self::find()->where(['scpm_id'=>$scpm_id,'is_active'=>1])->orderBy('preference_order ASC')->all();
        $dec_mapping_data = [];
        foreach ($dec_mapping as $key => $value) {
            $dec_mapping_data[] = [
                'id'=>$value->id,
                'id_desc'=>'dec_mapping_id',
                'key' => $value->dec_code,
                'field_name' => $value->declaration->declaration_text,
                'type'=>'checkbox',
                'disabled'=>false,
                'required' => true,
                'placeholder' => $value->declaration->declaration_text,
            ];
        }

        $data[] = [
                    'tab_id' => $stm_id,
                    'category'=>$category_name,
                    'fields'=>$dec_mapping_data
                ];

         
         return $data; 

    }

    
}
