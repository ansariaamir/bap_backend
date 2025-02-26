<?php

namespace app\models\masters;

use Yii;

/**
 * This is the model class for table "mst_userrole".
 *
 * @property int $id
 * @property string $role_type
 * @property string $role_name
 * @property string $role_name_label
 * @property string|null $short_code
 * @property string $created_on
 */
class MstUserrole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_userrole';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_type', 'role_name', 'role_name_label', 'created_on'], 'required'],
            [['created_on'], 'safe'],
            [['is_active'], 'integer'],
            [['role_name', 'role_name_label'], 'string', 'max' => 255],
            [['short_code','role_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_type' => 'Role Type',
            'role_name' => 'Role Name',
            'role_name_label' => 'Role Name Label',
            'short_code' => 'Short Code',
            'created_on' => 'Created On',
        ];
    }


/*
*  This function is use on serviceconfig workflow dropdown
*/
    public static function getallData($role_type=NULL){
        $query = self::find()->where(['is_active'=>1]);
        if($role_type){
            if($role_type=='BO'){
                $query->andWhere(['role_type'=>'BO']);
            }  

            // if($role_type=='BO-DO'){
            //     $query->andWhere(['in','role_type',['BO','DO']]);
            // }  
        }

        return $query->all();       
    }

/*
*  not use Q k previuos or next role ka consept ab nahi raha 
*/
    // public static function getroleDetails($role_id){
    //     if($role_id){
    //         $role_ids = explode(',', $role_id);
    //         $final_array = [];
    //         foreach ($role_ids as $key => $value) {
    //             $findData = self::find()->where(['id'=>$value])->one();
    //             $final_array[]=[
    //                 'role_id'=>$findData->id,
    //                 'role_name'=>$findData->role_name,
    //                 'role_name_label'=>$findData->role_name_label,
    //             ];
    //         }
    //         return $final_array;
    //     }else{
    //         return [
    //             'role_id'=>null,
    //             'role_name'=>null,
    //             'role_name_label'=>null,
    //         ];
    //     }
    // }
}
