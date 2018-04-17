<?php

    namespace backend\models;

    use Yii;
    use yii\base\Model;
    use yii\helpers\ArrayHelper;
    use backend\models\Document;
    use backend\models\DocumentPosition;
    
    /**
     *
     */
    class CreateBillForm extends Model
    {
        public $type;
        public $name;
        public $number;
        public $client_id;
        public $order_id;
        public $date;
        public $positions;
            
        public function rules()
        {
            return [
                [['name', 'number', 'client_id'], 'required'],
                [['order_id'], 'integer'],
                [['date', 'type','payment_type', 'sum', 'positions'], 'safe'],
                [['name', 'number'], 'string', 'max' => 255],
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'name' => 'Название',
                'number' => 'Номер счёта',
                'order_id' => 'Номер заказа',
                'date' => 'Дата счёта',
                'client_id' => 'Клиент',
            ];
        }
        
        public function getNextNumber()
        {
            $next = (new \yii\db\Query())
                    ->select(['number'])
                    ->from(Document::tableName())
                    ->where([
                        'type' => Document::TYPE_BILL,
                        'direction' => Document::FOR_CLIENT,
                    ])
                    ->andWhere("date >= '" . date('Y-01-01') . "'")
                    ->orderBy(['cast(`number` as unsigned)' => SORT_DESC])
                    ->one();
                         
            return (int) $next['number'] + 1;
        }
        
        public function create()
        {
            if (!$this->validate()) {
                foreach ($this->getErrors() as $key => $value) {
                    Yii::$app->session->setFlash('error', $value[0]);
                }
                return null;
            }
            
            $d = new Document;
            $d->type = Document::TYPE_BILL;
            $d->direction = Document::FOR_CLIENT;
            $d->name = $this->name;
            $d->number = $this->number;
            $d->date = $this->date;
            $d->client_id = $this->client_id;
            $d->order_id = $this->order_id;
            
            foreach ($this->positions AS $p) {
                $d->sum += $p['p'] * $p['q'];
            }
            
            $d->save();
            
            foreach ($this->positions AS $p) 
            {
                $i = new DocumentPosition;
                $i->document_id = $d->id;
                $i->name = $p['name'];
                $i->price = $p['p'];
                $i->quantity = $p['q'];
                $i->save();
            }
            
            return $d->id > 0 ? $d : null;
        }
    }