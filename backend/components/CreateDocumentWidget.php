<?php

	namespace backend\components;
    
    use Yii;
	use yii\base\Widget;
	use yii\helpers\Html;
	use yii\widgets\InputWidget;
	
    use backend\models\CreateBillForm;
    use backend\models\Document;

	class CreateDocumentWidget extends Widget
	{   
        public $type = 1;
        public $order;
        
        public function init()
		{
			parent::init();
		}

		public function run()
		{
            /**
             * Форма создания счёта
             */
            if ($this->type == Document::TYPE_BILL) 
            {
                $model = new CreateBillForm;
                
                if ($this->order) {
                    $model->client_id = $this->order->client_id;
                    $model->name = 'Счёт к заказу #' . $this->order->id;
                }
                
                if (Yii::$app->request->get('SearchClientForm')['client_id']) {
                    $model->client_id = (int) Yii::$app->request->get('SearchClientForm')['client_id'];
                }
                
                return $this->render('CreateBillForm', ['model' => $model, 'order' => $this->order]);
            }
            /**
             * Форма создания акта
             */
            elseif ($this->type == Document::TYPE_AKT) 
            {
            }
            /**
             * Форма создания накладной
             */
            elseif ($this->type == Document::TYPE_NAKL) 
            {
            }
        }
    }