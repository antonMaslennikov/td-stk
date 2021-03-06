<?php

namespace backend\controllers;

use Yii;
use backend\models\Document;
use backend\models\DocumentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller
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
     * Lists all Document models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Document model.
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
     * Creates a new Document model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->request->post('CreateBillForm'))
            $model = new \backend\models\CreateBillForm;
        elseif (Yii::$app->request->post('CreateAktForm'))
            $model = new \backend\models\CreateAktForm;

        if ($model->load(Yii::$app->request->post())) {
            if ($d = $model->create()) {
                return $this->redirect(['view', 'id' => $d->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Document model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Document model.
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
     * Скачать документ в формате xlsx
     * @param integer $id номер документа
     */
    public function actionDownload($id)
    {
        $doc = Document::findOne(['id' => $id]);
        
        $doc->download();
        
        exit('download');
    }
    
    /**
     * Оплата счёта
     */
    public function actionPay()
    {
        $model = DynamicModel::validateData(Yii::$app->request->post(), [
            [['id', 'sum'], required],
            ['sum', 'number'],
        ]);

        if ($model->hasErrors()) {
            // валидация завершилась с ошибкой
            foreach ($model->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
        } else {
            // Валидация успешно выполнена
            if ($D = Document::findOne($model->id)) {
                $D->pay($model->sum);
            }
        }
        
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
