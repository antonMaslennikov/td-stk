<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\Order;

class StatController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new StockItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionByproductcategorys()
    {
        if (Yii::$app->request->get('daterange')) 
        {
            $r = explode(' - ', Yii::$app->request->get('daterange'));
            
            $s = date('Y-m-d 00:00:00', strtotime($r[0]));
            $e = date('Y-m-d 23:59:59', strtotime($r[1]));
            
            $rs = Yii::$app->db
                    ->createCommand("select
                            p.sex,
                            c.id AS category_id,
                            c.name AS category_name,
                            oi.quantity,
                            oi.price,
                            oi.discount
                          from
                            `order` o,
                            `order__item` oi,
                            `product` p,
                            `category` c
                          where 
                                o.id = oi.order_id
                            AND oi.product_id = p.id
                            AND p.category_id = c.id
                            AND oi.is_deleted = 0
                            AND o.status = :status
                            AND o.delivered_date BETWEEN :start AND :end", 
                        [':start' => $s, ':end' => $e, ':status' => Order::STATUS_DELIVERED])
                    ->queryAll();
            
            $data = $total = [];
            
            foreach ($rs AS $r)
            {
                $cats[$r['category_id']] = $r['category_name'];
                $data[$r['category_id']][$r['sex']]['q'] += $r['quantity'];
                $data[$r['category_id']][$r['sex']]['s'] += ($r['price'] - ($r['price'] / 100 * $r['discount'])) * $r['quantity'];
                
                $data[$r['category_id']]['total']['q'] += $r['quantity'];
                $data[$r['category_id']]['total']['s'] += ($r['price'] - ($r['price'] / 100 * $r['discount'])) * $r['quantity'];
                
                $total[$r['sex']]['q'] += $r['quantity'];
                $total[$r['sex']]['s'] += ($r['price'] - ($r['price'] / 100 * $r['discount'])) * $r['quantity'];
                
                $total['total']['q'] += $r['quantity'];
                $total['total']['s'] += ($r['price'] - ($r['price'] / 100 * $r['discount'])) * $r['quantity'];
            }
            
            //printr($data);
        }
        
        return $this->render('byproductcategorys', ['data' => $data, 'cats' => $cats, 'total' => $total]);
    }
    
    public function actionSummary()
    {
        return $this->render('summary', []);
    }
    
    public function actionBymanagers()
    {
        return $this->render('bymanagers', []);
    }
}
