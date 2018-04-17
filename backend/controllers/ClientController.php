<?php

namespace backend\controllers;

use Yii;
use common\models\OrderClient;
use backend\models\OrderClientSearch;
use backend\models\OrderClientForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Url;

/**
 * OrderClientController implements the CRUD actions for OrderClient model.
 */
class ClientController extends Controller
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
     * Lists all OrderClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderClient model.
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
     * Creates a new OrderClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderClientForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($client = $model->saveClient()) {
                if (Yii::$app->request->post('goBack')) {
                    return Yii::$app->response->redirect(Yii::$app->request->post('goBack') . (strpos(Yii::$app->request->post('goBack'), '?') === false ? '?' : '&') . 'SearchClientForm[client_id]=' . $client->id);
                } else {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                if (Yii::$app->request->post('goBack')) {
                    return $this->redirect([Yii::$app->request->post('goBack')]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderClient model.
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
     * Deletes an existing OrderClient model.
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
     * Finds the OrderClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderClient::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    
    public function actionValidate() {
		
        $model = new \backend\models\OrderClientForm;
		
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
	}
    
    public function actionAutocomplite($term)
    {
        $results = [];
        
        /*
        $products = OrderClient::find()
                        ->where(['or',
                            ['like', 'name', $term],
                            ['like', 'phone', $term],
                            ['like', 'email', $term],
                            ])
                        ->limit(10)
                        ->all();
        */
        $products = (new \yii\db\Query())
                    ->select(['id', 'name', 'phone', 'email'])
                    ->from('order__client')
                    ->where(['or', 
                            ['like', 'name', $term],
                            ['like', 'phone', $term],
                            ['like', 'email', $term],
                            ])
                    ->limit(10)
                    ->all();
            
        foreach ($products AS $p) {
            $results[] = [
                //'value' => $p['id'], 
                'id' => $p['id'], 
                'label' => $p['name'] . ' ' . $p['email'] . ' ' . $p['phone']
            ];
        }
        
        return json_encode($results);
    }
}
