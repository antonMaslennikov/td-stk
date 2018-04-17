<?php

	namespace backend\components;

	use yii\base\Widget;
	use yii\helpers\Html;
	use yii\widgets\InputWidget;

	use backend\models\OrderAddItemForm;
	
	class AddItemWidget extends Widget
	{	
        public $containerOptions = [];
        public $order;
        
        public function init()
		{
			parent::init();
		}

		public function run()
		{
            $model = new OrderAddItemForm;
            $model->order = $this->order;
            $model->order_id = $this->order->id;
            
            return $this->render('AddItemFormForm', ['model' => $model]);
        }
    }