<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Document;
use common\models\OrderClient;
/**
 * DocumentSearch represents the model behind the search form of `backend\models\Document`.
 */
class DocumentSearch extends Document
{    
    public $dateStart;
    public $dateEnd;
    public $search;
    public $onlypayed;
    public $manager;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'name', 'number', 'order_id', 'sum', 'sum_payed', 'payed', 'manager'], 'integer'],
            [['type', 'direction', 'date', 'payment_type', 'quantity', 'order_status', 'dateStart', 'dateEnd', 'search', 'onlypayed'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'onlypayed' => 'только оплаченные',
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
        $query = Document::find()
                    ->select('{{document}}.*, COUNT({{document__position}}.`id`) AS quantity, {{order}}.`status` AS order_status, {{user}}.`username` AS manager')
                    ->joinWith('positions')
                    ->joinWith('client')
                    ->leftJoin('order', '{{order}}.`id` = {{document}}.`order_id`')
                    ->leftJoin('user', '{{user}}.`id` = {{document}}.`manager_id`')
                    ->groupBy('{{document}}.id');

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
        
        if ($this->dateStart) {
            $query->andFilterWhere([
                '>=', 'date', $this->dateStart
            ]);
        }
        
        if ($this->dateEnd) {
            $query->andFilterWhere([
                '<=', 'date', $this->dateEnd
            ]);
        }
        
        if ($this->onlypayed) {
            $query->andFilterWhere([
                'payed' => 1,
            ]);
        }
        
        if ($this->manager) {
            $query->andFilterWhere([
                'document.manager_id' => $this->manager,
            ]);
        }
        
        if ($this->search) {
            $query->andFilterWhere(['or', 
                                        ['=', 'document.id', $this->search],
                                        ['like', 'number', $this->search],
                                        ['like', 'document.name', $this->search],
                                        ['like', OrderClient::tableName() . '.inn', $this->search],
                                        ['like', OrderClient::tableName() . '.name', $this->search],
                                        ['like', OrderClient::tableName() . '.org', $this->search],
                                   ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            //'parent_id' => $this->parent_id,
            'name' => $this->name,
            'number' => $this->number,
            'order_id' => $this->order_id,
            //'sum' => $this->sum,
            //'sum_payed' => $this->sum_payed,
            
            '{{document}}.payment_type' => $this->payment_type,
            'direction' => $this->direction,
            'manager_id' => $this->manager_id,
        ]);


        return $dataProvider;
    }
}
