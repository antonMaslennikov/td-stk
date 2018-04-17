<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserSearch;
use backend\models\UserForm;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\RolesHelper;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
		if (!Yii::$app->user->can(RolesHelper::ADMIN)) {
			throw new \yii\web\ForbiddenHttpException('Нет доступа к данной странице');
		}
		
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		if (!Yii::$app->user->can(RolesHelper::ADMIN)) {
			throw new \yii\web\ForbiddenHttpException('Нет доступа к данной странице');
		}
		
		$form = new UserForm();
		
        if ($form->load(Yii::$app->request->post())) {
			
			if ($user = $form->signup()) {
				return $this->redirect(['update', 'id' => $user->id]);
			}
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
		if (!Yii::$app->user->can(RolesHelper::ADMIN) && Yii::$app->user->identity->id != $id) {
			throw new \yii\web\ForbiddenHttpException('Нет доступа к данной странице');
		}
		
		$model = new UserForm;
		$user = User::findOne(['id' => $id]);
		$model->setAttributes($user->getAttributes());
		
		if ($model->load(Yii::$app->request->post())) {
            
            $model->avatar = UploadedFile::getInstance($model, 'avatar');
            
			if ($model->updateUser()) {
				return $this->redirect(['update', 'id' => $model->id]);
			}
		}
		
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
