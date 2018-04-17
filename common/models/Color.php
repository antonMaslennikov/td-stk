<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "color".
 *
 * @property int $id
 * @property string $name
 * @property string $hex
 * @property int $group
 */
class Color extends \yii\db\ActiveRecord
{
	public static $groups = [
		0 => ['title' => 'Разное'],
		1 => ['title' => 'Светлые оттенки'],
		2 => ['title' => 'Тёмные оттенки'],
		3 => ['title' => 'Цветное'],
		4 => ['title' => 'Серые'],
	];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'hex', 'group'], 'required'],
            [['name'], 'string', 'max' => 30],
            [['hex'], 'string', 'max' => 7],
            [['group'], 'in', 'range' => array_keys(self::$groups)],
        ];
    }
	
	public function getGroups()
	{
		return ArrayHelper::getColumn(self::$groups, 'title');
	}
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'hex' => 'Hex',
            'group' => 'Группа',
        ];
    }
	
	
	public function beforeSave($insert)
    {
		 if (parent::beforeSave($insert)) {
			$this->hex = str_replace(['#'], '', $this->hex);
			return true;
		}
		return false;
	}
}
