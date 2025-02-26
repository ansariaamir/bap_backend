<?php 
namespace app\models;

use Yii;
use DateTimeImmutable;

class Token {
	public static function tokenGenerator($user_id){
        if(Yii::$app->user->identity->role_id){
            $role_id = Yii::$app->user->identity->role_id;
            $role_type = Yii::$app->user->identity->role_type;
            $user_name = Yii::$app->user->identity->name;
        }else{
            $userDetails = User::GetUserDetails($user_id);
            $role_id = $userDetails['role_id'];
            $role_type = $userDetails['role_type'];
            $user_name = $userDetails['name'];
        }
        
        $now = new DateTimeImmutable();
        $jwt = Yii::$app->get('jwt');
        
		$token = $jwt->builder()
                // Configures the issuer (iss claim)
                ->issuedBy('https://baptwo.com')
                // Configures the audience (aud claim)
                ->permittedFor('BAPTWO')
                // Configures the id (jti claim)
                ->identifiedBy($user_id)
                // Configures the time that the token was issue (iat claim)
                ->issuedAt($now)
                // Configures the time that the token can be used (nbf claim)
                ->canOnlyBeUsedAfter($now)
                // Configures the expiration time of the token (exp claim)
                ->expiresAt($now->modify('+1 hour'))
                // Configures a new claim, called "uid"
                ->withClaim('uid', $user_id)
                ->withClaim('role_id', $role_id)
                ->withClaim('role_type', $role_type)
                ->withClaim('name', $user_name)
                // Returns a signed token to be used
                ->getToken($jwt->signer(), $jwt->key())
                // Convert token to string
                ->toString();

         return $token;
	}
}
?>