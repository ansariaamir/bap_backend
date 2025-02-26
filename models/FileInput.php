<?php 

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class FileInput extends Model{

	public $file_url;

	public function rules(){
		return [
			[['file_url'],'file','skipOnEmpty'=>false,'extensions'=>'xlsx, xls'],
		];
	}

	public function upload(){
		if($this->validate()){
			// save code or other code you want
			return true;
		}else{
			return false;
		}
	}
}

?>