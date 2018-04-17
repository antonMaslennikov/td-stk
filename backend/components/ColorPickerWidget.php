<?php

	namespace backend\components;

	use yii\base\Widget;
	use yii\helpers\Html;
	use yii\widgets\InputWidget;

	use common\models\color;
	
	class ColorPickerWidget extends InputWidget
	{	
		public $addon = '<i class="fa fa-paint-brush"></i>';
		
		public $template = '{input-hidden}{input}{addon}';
		
		public $containerOptions = [];
		
		public $colors;
		
		public function init()
		{
			parent::init();
			
			Html::addCssClass($this->options, 'form-control');
			Html::addCssClass($this->containerOptions, 'input-group myColorpicker-component');
		}

		public function run()
		{
			$cc = [];
			
			foreach (Color::find()->all() AS $c) {
				$cc[] = Html::tag('a', "&nbsp;", ['class' => 'color-block', 'data-id' => $c->id, 'data-name' => $c->name, 'style' => 'background-color:#' . $c->hex]);
				$this->colors[$c->id] = $c;
			}
			
			$colors = Html::tag('div', implode('', $cc), ['class' => 'mycolorpicker-colors']);
			
			$input  = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
			$inputH = Html::textInput('color-name', $this->colors[$this->model->color_id]->name, ['class' => 'form-control', 'readonly' => 'readonly']);

			$addon = Html::tag('span', $this->addon . $colors, ['class' => 'input-group-addon']);
            $input = strtr($this->template, [
				'{input}' => $input, 
				'{input-hidden}' => $inputH, 
				'{addon}' => $addon,
			]);
			
            $input = Html::tag('div', $input, $this->containerOptions);	
			
			echo $input;
			
			$view = $this->getView();
			$view->registerJs("$('.myColorpicker-component span i').click(function() { $('.mycolorpicker-colors').toggle(); });");
			$view->registerJs("$('.mycolorpicker-colors a').click(function(){ var self = $(this); $('input[name=color-name]').val(self.data('name')); $('#productform-color_id').val(self.data('id')); $('.mycolorpicker-colors').hide(); })");
		}
	}

?>