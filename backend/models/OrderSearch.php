<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Order;

/**
 * OrderSearch represents the model behind the search form of `backend\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'address_id'], 'integer'],
            [['payment_type', 'delivery_type', 'created_at', 'manager'], 'safe'],
        ];
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
        ]);

        $query->andFilterWhere(['like', 'payment_type', $this->payment_type])
            ->andFilterWhere(['like', 'delivery_type', $this->delivery_type]);

        $query->orderBy([
            'order.id' => SORT_DESC,
        ]);
        
        return $dataProvider;
    }
}
