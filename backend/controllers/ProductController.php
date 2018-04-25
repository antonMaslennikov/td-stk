<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Product;
use backend\models\ProductSearch;
use backend\models\ProductForm;
use backend\models\ProductImportForm;
use common\components\RolesHelper;
use common\models\Picture;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
			/*
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
					[
						'actions' => ['import'],
                        'allow' => true,
                        'roles' => [RolesHelper::ADMIN]
                    ],
                ],
            ],
			*/
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductForm();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->pictures = UploadedFile::getInstances($model, 'pictures');
            
			if ($product = $model->saveProduct())
				return $this->redirect(['view', 'id' => $product->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
		$model = new ProductForm();
		
        $product = $this->findModel($id);

		$model->setAttributes($product->getAttributes());
		
        if ($model->load(Yii::$app->request->post())) {
            
            $model->pictures = UploadedFile::getInstances($model, 'pictures');
           
            if ($model->saveProduct()) {
                if (Yii::$app->request->post('apply') === '')
                    return $this->redirect(['update', 'id' => $model->id]);
                else
                    return $this->redirect(['index']);
            }
        }
		$a = 2;
        
        return $this->render('update', compact('model', 'product', 'a'));
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	public function actionImport()
    {
		if (!Yii::$app->user->can(RolesHelper::ADMIN)) {
			throw new \yii\web\ForbiddenHttpException('Нет доступа к данной странице');
		}
		
		$model = new ProductImportForm;
		
		if ($model->load(Yii::$app->request->post())) {
			$model->file = UploadedFile::getInstance($model, 'file');
			if ($model->import()) {
				return $this->redirect(['import']);
			}
        }
		
		return $this->render('import', [
            'model' => $model,
        ]);
	}
    
    public function actionDeletepicture() {
        
        if ($p = Picture::findOne([Yii::$app->request->get('id')])) {
            $p->delete();
            
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }
	
    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionAutocomplite($term)
    {
        $results = [];
        
        $products = (new \yii\db\Query())
                    ->select(['product.id', 'product.name_ru', 'product.art', 'category.name AS category_name', 'color.name AS color_name', 'sizes.name AS size_name'])
                    ->from('product')
                    ->leftJoin('category', 'product.category_id = category.id')
                    ->leftJoin('color', 'product.color_id = color.id')
                    ->leftJoin('sizes', 'product.size_id = sizes.id')
                    ->where([
                        'and', 
                        ['product.status' => Product::STATUS_ACTIVE], 
                        ['or', 
                            ['like', 'product.name_ru', $term],
                            ['like', 'product.art', $term]
                        ]
                    ])
                    ->limit(10)
                    ->all();
            
        foreach ($products AS $p) {
            $results[] = [
                //'value' => $p['id'], 
                'id' => $p['id'], 
                'label' => $p['name_ru'] . ', Категория: ' . $p['category_name'] . ', Цвет: ' . $p['color_name'] . ', Размер: ' . $p['size_name'] . ', Артикул: ' . $p['art']];
        }
        
        return json_encode($results);
    }
    
    public function actionTostock($id, $q)
    {
        if (empty($q)) {
            $q = 1;
        }
        
        if ($id && $q)
        {
            for ($i = 0; $i < $q; $i++) {
                $si = new \backend\models\StockItem;
                $si->product_id = $id;
                $si->save();
            }
            
            Yii::$app->session->setFlash('success', 'На склад внесено ' .$q . ' позиций');
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }
}
