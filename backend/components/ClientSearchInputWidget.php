<?php

	namespace backend\components;

	use yii\base\Widget;
	use yii\helpers\Html;
	use yii\widgets\InputWidget;

	use common\models\OrderClient;
	
	class ClientSearchInputWidget extends InputWidget
	{	
        public $containerOptions = [];
        
        public $client;
        
        public function init()
		{
			parent::init();
			
            if ($this->client) {
                $this->options['value'] = $this->client->id;
            }
            
			Html::addCssClass($this->options, 'form-control');
			Html::addCssClass($this->containerOptions, 'input-group');
		}

		public function run()
		{
            $inputH = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
			$inputT = Html::textInput('color-name', $this->client->name . ' ' . $this->client->email . ' ' . $this->client->phone, ['class' => 'form-control', 'readonly' => 'readonly']);
            $button = Html::tag('div', Html::a('Найти или добавить', '#searchClientModal', ['class' => 'btn btn-danger', 'data-toggle' => 'modal']), ['class' => 'input-group-btn']);
            $input  = Html::tag('div', $inputH . $inputT . $button, ['class' => 'input-group']);
            
            echo $input;
        }
    }