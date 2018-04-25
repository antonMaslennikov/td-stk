<?php
	use common\components\RolesHelper;
    use yii\helpers\Url;
    use backend\models\Document;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <? if (Yii::$app->user->identity->avatar): ?>
                <img src="<?= Url::toRoute(Yii::$app->user->identity->avatar) ?>" class="img-circle" alt="User Image"/>
                <? else: ?>
                <img src="/admin/img/no-avatar.jpg" class="img-circle" alt="User Image"/>
                <? endif; ?>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->fio ? Yii::$app->user->identity->fio : Yii::$app->user->identity->username ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form --
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        !-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
					
                    ['label' => '', 'options' => ['class' => 'header']],
					['label' => 'Главная', 'icon' => 'dashboard', 'url' => ['/']],
					[
						'label' => 'Заказы', 
						'icon' => 'shopping-cart', 
						'url' => '#',
						'items' => [
							['label' => 'Список', 'icon' => 'list', 'url' => ['/order'],],
							['label' => 'Добавить новый', 'icon' => 'plus', 'url' => ['/order/create'],],
						],
					],
                    ['label' => 'Производство', 'icon' => 'magic', 'url' => '#',
                        'items' => [
                            ['label' => 'Пошив', 'icon' => 'scissors', 'url' => ['production/sewing']],
                            ['label' => 'Печать', 'icon' => 'print', 'url' => ['production/printing']],
                        ]
                    ],
                    ['label' => 'Документы', 'icon' => 'clone', 'url' => '#',
                        'items' => [
                            ['label' => 'Счета', 'icon' => 'list', 'url' => ['document/index', 'DocumentSearch[type]' => Document::TYPE_BILL, 'DocumentSearch[direction]' => 1]],
                            ['label' => 'Акты', 'icon' => 'list', 'url' => ['document/index', 'DocumentSearch[type]' => Document::TYPE_AKT, 'DocumentSearch[direction]' => 1]],
                            ['label' => 'Накладные', 'icon' => 'list', 'url' => ['document/index', 'DocumentSearch[type]' => Document::TYPE_NAKL, 'DocumentSearch[direction]' => 1]],
                        ]
                    ],
					[
						'label' => 'Товары', 
						'icon' => 'tags', 
						'url' => '#',
						'items' => [
							['label' => 'Список', 'icon' => 'list', 'url' => ['/product'],],
							['label' => 'Добавить новый', 'icon' => 'plus', 'url' => ['/product/create'],],
                            ['label' => 'Склад', 'icon' => 'list', 'url' => ['/stock'],],
							['label' => 'Импорт', 'icon' => 'download', 'url' => ['/product/import'], 'visible' => Yii::$app->user->can(RolesHelper::ADMIN)],
						],
					],
                    ['label' => 'Клиенты', 'icon' => 'users', 'url' => ['/client']],
					[
						'label' => 'Справочники', 
						'icon' => 'folder-open', 
						'url' => '#',
						'items' => [
							['label' => 'Категории товаров', 'icon' => 'sitemap', 'url' => ['/category']],
							['label' => 'Цвета', 'icon' => 'paint-brush', 'url' => ['/color']],
                            ['label' => 'Виды ткани', 'icon' => 'sticky-note-o', 'url' => ['/material']],
                            ['label' => 'Размеры', 'icon' => 'arrows', 'url' => ['/size']],
						],
					],
                ],
            ]
        ) ?>

    </section>

</aside>
