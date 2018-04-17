<?php

use yii\db\Migration;

/**
 * Handles the creation of table `categorys`.
 */
class m180327_150254_create_categorys_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
			//'tree' => $this->integer()->notNull(),
			'lft' => $this->integer()->notNull(),
			'rgt' => $this->integer()->notNull(),
			'depth' => $this->integer()->notNull(),
			'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('categorys');
    }
}
