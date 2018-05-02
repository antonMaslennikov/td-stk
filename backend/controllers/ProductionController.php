<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Production;
use backend\models\ProductionItems;

/**
 * SizeController implements the CRUD actions for Size model.
 */
class ProductionController extends Controller
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

    
    public function actionSewing()
    {
        return $this->render('sewing', [
            'dataProvider' => Production::sewingSearch(Yii::$app->request->queryParams),
            'grouped' => Yii::$app->request->get('group') ? Production::sewingGrouped() : null,
        ]);
    }

    public function actionPrinting()
    {
        return $this->render('printing', [
            'dataProvider' => Production::printingSearch(Yii::$app->request->queryParams),
        ]);
    }
    
    /**
     * Отправить позицию производства в печать
     */
    public function actionMove2printing($id)
    {
        $model = ProductionItems::findOne($id);
        
        if ($model->move2printing()) {
            Yii::$app->session->setFlash('success', 'Позиция отправлена в печать');
            $this->redirect('sewing');
        }
    }
    
    /**
     * Отправить позицию производства в резерв (произведено)
     * используется для чистого в очереди пошива или в очереди печати 
     */
    public function actionMove2reserv($id)
    {
        $model = ProductionItems::findOne($id);
        if ($model->move2reserv()) {
            Yii::$app->session->setFlash('success', 'Позиция отправлена в резерв на склад');
            return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
        }
    }
    
    /**
     * Взять со склада имеющееся количество готовых чистых позиций для производства позиции с печатью
     */
    public function actionTakefromclear($id)
    {
        $model = ProductionItems::findOne($id);
        if ($model->takefromclear()) {
            
        }
        return $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
    }
}
