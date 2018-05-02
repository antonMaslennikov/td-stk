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
        if (Yii::$app->request->get('daterange')) 
        {
            $r = explode(' - ', Yii::$app->request->get('daterange'));
            
            $s = date('Y-m-d 00:00:00', strtotime($r[0]));
            $e = date('Y-m-d 23:59:59', strtotime($r[1]));
            
            $data = Yii::$app->db
                    ->createCommand("select
                            COUNT(DISTINCT(o.`id`)) AS c,
                            SUM(oi.quantity) AS q,
                            SUM(oi.price - (oi.price / 100 * oi.discount) + o.delivery_cost) AS s,
                            o.delivery_type
                          from
                            `order` o,
                            `order__item` oi
                          where 
                                oi.is_deleted = 0
                            AND o.status = :status
                            AND o.delivered_date BETWEEN :start AND :end
                         GROUP BY o.delivery_type", 
                        [':start' => $s, ':end' => $e, ':status' => Order::STATUS_DELIVERED])
                    ->queryAll();
            
            $total = [];
            
            foreach ($data AS $r)
            {
                $total['c'] += $r['c'];
                $total['q'] += $r['q'];
                $total['s'] += $r['s'];
            }
            
            //printr($data);
        }
        
        return $this->render('summary', ['data' => $data, 'total' => $total]);
    }
    
    public function actionBymanagers()
    {
        if (Yii::$app->request->get('year'))
            $y = (int) Yii::$app->request->get('year');
        else
            $y = date('Y');
        
        $s = date($y . '-01-01 00:00:00');
        $e = date($y . '-12-31 23:59:59');
        
        $rs = Yii::$app->db
                    ->createCommand("
                          SELECT
                            SUM(l.result) AS s,
                            EXTRACT(MONTH FROM l.time) AS m,
                            COUNT(DISTINCT(o.id)) AS c,
                            o.manager_id,
                            u.username
                          FROM
                            {{order}} o,
                            {{user}} u,
                            {{order__log}} l
                          WHERE 
                                l.time BETWEEN :start AND :end
                            AND l.action = 'set_payment'
                            AND o.manager_id = u.id
                            AND o.id = l.order_id
                          GROUP BY CONCAT(EXTRACT(MONTH FROM l.time), o.manager_id)", 
                        [':start' => $s, ':end' => $e])
                    ->queryAll();
        
        $data = $total = [];
        
        foreach($rs AS $row)
        {
            if (!$managers[$row['manager_id']]) {
                $managers[$row['manager_id']] = ['name' => $row['username']];
            }
            
            $managers[$row['manager_id']]['total_q'] += $row['c'];
            $managers[$row['manager_id']]['total_s'] += $row['s'];
            
            if (!$data[$row['m']]) {
                $data[$row['m']] = ['name' => date('F', strtotime(date('Y-' . $row['m'] . '-d')))];
            }
            
            $data[$row['m']]['tmp'][$row['manager_id']] = ['c' => $row['c'], 's' => $row['s']];
        }
        
        foreach($data AS $km => $m)
        {
            foreach ($managers AS $mid => $m) {
                if ($data[$km]['tmp'][$mid]) {
                    $data[$km]['managers'][$mid] = $data[$km]['tmp'][$mid];
                } else {
                    $data[$km]['managers'][$mid] = ['c' => '-', 's' => '-'];
                }
            }
        }
        
        return $this->render('bymanagers', ['data' => $data, 'total' => $total, 'managers' => $managers, 'year' => $y]);
    }
}
