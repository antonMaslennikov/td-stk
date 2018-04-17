<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
/**
 * ������������� RBAC ����������� � ������� php yii rbac/init
 */
class RbacController extends Controller {

    public function actionInit() {
        $auth = Yii::$app->authManager;
        
        $auth->removeAll(); //�� ������ ������ ������� ������ ������ �� ��...
        
        // �������� ���� ������ � ��������� ��������
        $admin = $auth->createRole('admin');
		$manager = $auth->createRole('manager');
        $visitor = $auth->createRole('guest');
        
        // ������� �� � ��
        $auth->add($admin);
		$auth->add($manager);
        $auth->add($visitor);
        
        // ������� ����������. ��������, �������� ������� viewAdminPage � �������������� ������� updateNews
        $viewAdminPage = $auth->createPermission('viewAdminPage');
        $viewAdminPage->description = '�������� �������';
        
        // ������� ��� ���������� � ��
        $auth->add($viewAdminPage);
        
        // ������ ������� ������������. ��� ���� visitor �� ������� ���������� updateNews,
        // � ��� ������ ������� ������������ �� ���� visitor � ��� ������� ����������� ���������� viewAdminPage
        
        // ���� ��������� �������� ����������� ���������� ��������������� �������
        //$auth->addChild($visitor,$updateNews);

        // ����� ��������� ���� ��������� ��������. �� �� �����, ������ ����� ��! :D
        $auth->addChild($admin, $manager);
        
        // ��� ����� ����� ����������� ���������� - ��������� �������
        $auth->addChild($admin, $viewAdminPage);

        // ��������� ���� admin ������������
        $auth->assign($admin, 1); 
		$auth->assign($admin, 2); 
    }
}