<?php

namespace common\components;

use common\models\User;
use Yii;
use yii\base\BaseObject;
use yii\rbac\Role;

/**
 * Class RolesHelper
 * @package common\components
 */
class RolesHelper extends BaseObject
{
    const GUEST = 'guest';
    const ADMIN = 'admin';
    const MANAGER = 'manager';

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            self::GUEST => 'Гость',
            self::MANAGER => 'Менеджер',
			self::ADMIN => 'Администратор',
        ];
    }

    public static function getChildList() {

        return [
            self::GUEST => [],
            self::ADMIN => [
                self::MANAGER,
            ],
            self::MANAGER => [
                self::GUEST,
            ],
        ];
    }

    /**
     * @param string $value
     * @return string|null
     */
    public static function getValue($value)
    {
        $list = self::getList();

        return isset($list[$value]) ? $list[$value] : null;
    }


    /**
     * @param string $role
     * @param int $user_id
     * @throws \yii\base\InvalidConfigException
     */
    public static function revoke($role, $user_id) {

        $roleObject = \Yii::createObject([
            'class'=>Role::className(),
            'name' => $role,
        ]);

        Yii::$app->authManager->revoke($roleObject, $user_id);
    }


    /**
     * @param string $role
     * @param int $user_id
     * @throws \Exception
     */
    public static function assign($role, $user_id) {

        $roleObject = Yii::createObject([
            'class'=>Role::className(),
            'name' => $role,
        ]);

        Yii::$app->authManager->assign($roleObject, $user_id);
    }


    /**
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function assignAll() {

        $data = User::find()
            ->select(['id', 'role'])
            ->asArray()
            ->all();

        $roles = array();

        foreach ($data as $row) {

            $role_id = $row['role'];

            if (!isset($roles[$role_id])) {

                $roles[$role_id] = Yii::createObject([
                    'class'=>Role::className(),
                    'name' => $role_id,
                ]);
            }

            Yii::$app->authManager->assign($roles[$role_id], $row['id']);
        }
    }
}