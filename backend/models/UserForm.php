<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\components\RolesHelper;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * Signup form
 */
class UserForm extends Model
{
	public $id;
    public $username;
	public $fio;
    public $email;
    public $password;
	public $status;
	public $role;
    public $avatar;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required',],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'filter' => function ($query) {
                if ($this->id) {
					$query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],
			
			['fio', 'string', 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.', 'filter' => function ($query) {
                if ($this->id) {
                    $query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
			
			['status', 'in', 'range' => array_keys(User::getStatuses())],
			
			['role', 'in', 'range' => array_keys(RolesHelper::getList())],
			
            ['avatar', 'file', 'extensions' => 'png, jpg, gif', 'skipOnEmpty' => true],
            
            ['password', 'string', 'min' => 6, 'skipOnEmpty' => true],
			
			['id', 'safe'],
        ];
    }
	
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
		$user->fio = $this->fio;
        $user->email = $this->email;
        if ($this->role && \Yii::$app->user->can(RolesHelper::ADMIN)) {
		  $user->role = $this->role;
        }
        if ($this->status && \Yii::$app->user->can(RolesHelper::ADMIN)) {
		  $user->status = $this->status;
        }
        $user->setPassword($this->password);
        $user->generateAuthKey();
      
        return $user->save() ? $user : null;
    }
	
	public function updateUser()
    {	
        if (!$this->validate()) {
            return null;
        }
        
		$user = User::findOne(['id' => $this->id]);

        $user->username = $this->username;
		$user->fio = $this->fio;
        $user->role = $this->role;
		$user->email = $this->email;
		$user->status = $this->status;
		
		if ($this->password) {
			$user->setPassword($this->password);
		}
		
        $user->generateAuthKey();
        
        if ($this->avatar && !$this->avatar->error) {
            $user->avatar = 'uploads/' . date('Y/m/d/') . $this->avatar->name;

            FileHelper::createDirectory(dirname($user->avatar));

            $this->avatar->saveAs($user->avatar);
			
			Image::thumbnail(\Yii::getAlias('@webroot') . '/' . $user->avatar, 160, 160)
				->save(\Yii::getAlias('@webroot') . '/' . $user->avatar, ['quality' => 90]);
        }
        
        return $user->save() ? $user : null;
    }
	
	
	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
			'fio' => 'Фио',
			'password' => 'Пароль',
            'role' => 'Группа',
			'status' => 'Статус',
            'avatar' => 'Аватар',
        ];
    }
}
