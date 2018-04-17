<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
/**
 * Инициализатор RBAC выполняется в консоли php yii rbac/init
 */
class RbacController extends Controller {

    public function actionInit() {
        $auth = Yii::$app->authManager;
        
        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...
        
        // Создадим роли админа и редактора новостей
        $admin = $auth->createRole('admin');
		$manager = $auth->createRole('manager');
        $visitor = $auth->createRole('guest');
        
        // запишем их в БД
        $auth->add($admin);
		$auth->add($manager);
        $auth->add($visitor);
        
        // Создаем разрешения. Например, просмотр админки viewAdminPage и редактирование новости updateNews
        $viewAdminPage = $auth->createPermission('viewAdminPage');
        $viewAdminPage->description = 'Просмотр админки';
        
        // Запишем эти разрешения в БД
        $auth->add($viewAdminPage);
        
        // Теперь добавим наследования. Для роли visitor мы добавим разрешение updateNews,
        // а для админа добавим наследование от роли visitor и еще добавим собственное разрешение viewAdminPage
        
        // Роли «Редактор новостей» присваиваем разрешение «Редактирование новости»
        //$auth->addChild($visitor,$updateNews);

        // админ наследует роль редактора новостей. Он же админ, должен уметь всё! :D
        $auth->addChild($admin, $manager);
        
        // Еще админ имеет собственное разрешение - «Просмотр админки»
        $auth->addChild($admin, $viewAdminPage);

        // Назначаем роль admin пользователю
        $auth->assign($admin, 1); 
		$auth->assign($admin, 2); 
    }
}