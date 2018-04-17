<?php

	namespace backend\components;

	use yii\base\Widget;
	use yii\helpers\Html;
	use yii\widgets\InputWidget;

	use backend\models\OrderClientForm;
	use backend\models\SearchClientForm;

	class ClientSearchWidget extends Widget
	{	
        public $containerOptions = [];
        public $goBack;
        
        public function init()
		{
			parent::init();
            $url = parse_url(urldecode($this->goBack));
            parse_str($url['query'], $url['query']);
            unset($url['query']['SearchClientForm']);
            $url['query'] = http_build_query($url['query']);
            $this->goBack = $url['path'] . '?' . $url['query'];
		}

		public function run()
		{
            $model = new OrderClientForm;
            $search = new SearchClientForm;
            
            return $this->render('ClienSearchForm', ['model' => $model, 'search' => $search, 'goBack' => $this->goBack]);
        }
    }