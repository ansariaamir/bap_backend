<?php

namespace app\models;
use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public function attributes()
    {
        return ['id', 'email','name','password_hash','auth_key','user_image','role_id','dept_id','state_id','district_id','role_name','role_type','dept_name','state_name','district_name','mobile_no','status','created_at','created_by','updated_at','updated_by']; // Include all attributes of your model
    }

public function rules()
{
    return [
        [['email'], 'required'],
        [['id'], 'integer'], 
        [['email'], 'email'],
        [['name'],'string']
        // other rules
    ];
}

    

    public static function tableName()
    {
        return '{{%users}}'; // Adjust table name as per your database structure
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user_id = $id;
        $userDetails = self::GetUserDetails($user_id);
        return $userDetails ? new static($userDetails) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user_id = $token->claims()->get('uid');
        $userDetails = self::GetUserDetails($user_id);
        return $userDetails ? new static($userDetails) : null;
        //return static::findOne(['id' => $user_id]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['email' => $username,'status'=>1])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function GetUserDetails($user_id){
        $userDetails =  Yii::$app->db->createCommand("SELECT u.id, u.email, u.name, u.user_image , up.role_id, up.dept_id, up.state_id, up.district_id, ur.role_name, ur.role_type, d.dept_name as dept_name, state.name as state_name, district.name as district_name
            FROM users u 
            INNER JOIN user_profile up on u.id=up.user_id
            INNER JOIN mst_userrole ur on up.role_id=ur.id
            LEFT JOIN mst_departments d on up.dept_id=d.id
            LEFT JOIN mst_option_value state on up.state_id=state.id
            LEFT JOIN mst_option_value district on up.district_id=district.id
            WHERE u.id=:uid")->bindValue(':uid',$user_id)->queryOne();
        return $userDetails;
       
    }
}
