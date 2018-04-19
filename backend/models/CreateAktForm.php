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
    class CreateAktForm extends Model
    {
        public $type;
        public $name;
        public $number;
        public $date;
        public $positions;
        public $parent;
        public $parent_id;
            
        public function rules()
        {
            return [
                [['type', 'name', 'number', 'parent_id'], 'required'],
                [['date', 'positions'], 'safe'],
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
                'number' => 'Номер',
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
                        'type' => $this->type,
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
            
            $parent = Document::findOne($this->parent_id);
            
            $d = new Document;
            $d->type = $this->type;
            $d->direction = Document::FOR_CLIENT;
            $d->name = $this->name;
            $d->number = $this->number;
            $d->date = $this->date;
            $d->client_id = $parent->client_id;
            $d->order_id = $parent->order_id;
            $d->parent_id = $this->parent_id;
            
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