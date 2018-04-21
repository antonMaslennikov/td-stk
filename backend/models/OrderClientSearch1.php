<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OrderClient;

/**
 * OrderClientSearch1 represents the model behind the search form of `common\models\OrderClient`.
 */
class OrderClientSearch1 extends OrderClient
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'email', 'phone', 'org', 'bank', 'bik', 'ks', 'rs', 'kpp', 'inn', 'dir', 'address', 'orgn', 'okpo', 'okato', 'created_at'], 'safe'],
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
        $query = OrderClient::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'org', $this->org])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'bik', $this->bik])
            ->andFilterWhere(['like', 'ks', $this->ks])
            ->andFilterWhere(['like', 'rs', $this->rs])
            ->andFilterWhere(['like', 'kpp', $this->kpp])
            ->andFilterWhere(['like', 'inn', $this->inn])
            ->andFilterWhere(['like', 'dir', $this->dir])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'orgn', $this->orgn])
            ->andFilterWhere(['like', 'okpo', $this->okpo])
            ->andFilterWhere(['like', 'okato', $this->okato]);

        return $dataProvider;
    }
}
