<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use backend\models\Order;

/**
 * OrderSearch represents the model behind the search form of `backend\models\Order`.
 */
class OrderSearch extends Order
{
    public $create_period;
    public $delivery_period;
    public $nonpayedonly;
    public $payedonly;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'address_id'], 'integer'],
            [['status', 'payment_type', 'delivery_type', 'created_at', 'manager_id', 'create_period', 'delivery_period', 'nonpayedonly', 'payedonly'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(Order::attributeLabels(), [
            'nonpayedonly' => 'только НЕ оплаченные',
            'payedonly' => 'только оплаченные',
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()
                    ->select('{{order}}.*, {{user}}.`username` AS manager')
                    ->leftJoin('user', '{{user}}.`id` = {{order}}.`manager_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->joinWith('client');

        // grid filtering conditions
        $query->andFilterWhere([
            'order.id' => $this->id,
            'client_id' => $this->client_id,
            'address_id' => $this->address_id,
            'created_at' => $this->created_at,
            'manager_id' => $this->manager_id,
        ]);
        
        if ($this->create_period) {
            $p = explode(' - ', $this->create_period);
            $query->andFilterWhere(['between', 'order.created_at', date('Y-m-d 00:00:00', strtotime(trim($p[0]))), date('Y-m-d 23:59:59', strtotime(trim($p[1])))]);
        }
        
        if ($this->delivery_period) {
            $p = explode(' - ', $this->delivery_period);
            $query->andFilterWhere(['between', 'order.delivery_date', date('Y-m-d 00:00:00', strtotime(trim($p[0]))), date('Y-m-d 23:59:59', strtotime(trim($p[1])))]);
        }

        $query
            ->andFilterWhere(['in', 'order.status', $this->status])
            ->andFilterWhere(['in', 'payment_type', $this->payment_type])
            ->andFilterWhere(['in', 'delivery_type', $this->delivery_type]);

        if ($this->payedonly) {
            $query->andFilterWhere(['payment_confirm' => 1]);
        }
        
        if ($this->nonpayedonly) {
            $query->andFilterWhere(['payment_confirm' => 0]);
        }
        
        $query->orderBy([
            'order.id' => SORT_DESC,
        ]);
        
        return $dataProvider;
    }
    
    public function getManagersList()
    {
        $managers = \common\models\user::find()
            ->select('username')
            ->where(['role' => \common\components\RolesHelper::MANAGER])
            ->indexBy('id')
            ->column();
        
        return $managers;
    }
}
