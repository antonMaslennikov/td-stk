<?php

namespace backend\controllers;

use Yii;
use backend\models\Order;
use backend\models\OrderSearch;
use backend\models\OrderForm;

use common\models\OrderClient;
use backend\models\OrderItem;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                    'updateitems' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderForm;

        if (Yii::$app->request->get('SearchClientForm')['client_id']) {
            $model->client = OrderClient::find()->where(['id' => Yii::$app->request->get('SearchClientForm')['client_id']])->one();
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($order = $model->createOrder())
                return $this->redirect(['view', 'id' => $order->id]);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
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
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * Добавить товар в корзину
     */
    public function actionAdditem()
    {
        $model = new \backend\models\OrderAddItemForm;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($item = $model->addItem())
                return $this->redirect(['view', 'id' => $model->order_id]);
        }
    }
    
    public function actionUpdateitems()
    {
        foreach (Yii::$app->request->post('pos') AS $id => $i)
        {
            $item = OrderItem::find()->where(['id' => $id])->one();
            
            $item->price = $i['price'];
            $item->quantity = $i['quantity'];
            $item->discount = $i['discount'];
                
            $item->save();
        }
        
        return $this->redirect(['view', 'id' => Yii::$app->request->post('order_id')]);
    }
    
    public function actionDeletepos($id)
    {
        $item = OrderItem::find()->where(['id' => $id])->one();
            
        $item->is_deleted = 1;

        $item->save();
        
        return $this->redirect(['view', 'id' => $item->order_id]);
    }
    
    public function actionAddcomment()
    {
        $model = new \backend\models\OrderCommentForm;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($item = $model->addComment())
                return $this->redirect(['view', 'id' => $model->order_id]);
        }
    }
    
    public function actionSetpayment()
    {
        $model = new \backend\models\OrderPayForm;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($item = $model->setPayment())
                return $this->redirect(['view', 'id' => $model->order_id]);
        }
    }
    
    public function actionSavedelivery()
    {
        $model = new \backend\models\OrderDeliveryForm;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($item = $model->saveData())
                return $this->redirect(['view', 'id' => $model->order_id]);
        }
    }
    
    public function actionSavepayment()
    {
        $model = new \backend\models\OrderPaymentForm;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($item = $model->saveData())
                return $this->redirect(['view', 'id' => $model->order_id]);
        }
    }
    
    public function actionPut2reserv($item_id)
    {
        if ($model = OrderItem::findOne($item_id)) {
            if ($model->put2reserv()) {
                Yii::$app->session->setFlash('success', 'Позиция #' . $model->id . ' отправлена в резерв');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Позиция с таким номером не обнаружена!');
        }
        
        return $this->redirect(['view', 'id' => $model->order_id]);
    }
    
    public function actionPut2production($item_id)
    {
        if ($model = OrderItem::findOne($item_id)) {
            if ($model->put2production()) {
                Yii::$app->session->setFlash('success', 'Позиция #' . $model->id . ' отправлена в производство');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Позиция с таким номером не обнаружена!');
        }
        
        return $this->redirect(['view', 'id' => $model->order_id]);
    }
    
    public function actionChstatus()
    {
        if ($model = Order::findOne(Yii::$app->request->post('id'))) {
            try
            {
                $model->changeStatus(Yii::$app->request->post('ch-status'));
                Yii::$app->session->setFlash('success', 'Статус заказа успешно изменён');
                
            } 
            catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessaage());
            }
        } else {
            Yii::$app->session->setFlash('error', 'Заказ с таким номером не обнаружена!');
        }
        
        return $this->redirect(['view', 'id' => $model->id]);
    }
    
    public function actionApplication($id)
    {
        $model = $this->findModel($id);
        
        $model->donwnloadApplication();
    }
}
