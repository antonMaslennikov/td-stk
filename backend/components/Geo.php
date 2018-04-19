<?php

    namespace backend\components;

    use yii\base\Model;

    class Geo extends Model
    {
        public static function getCountrys() 
        {
            $countrys = (new \yii\db\Query())
                    ->select(['name'])
                    ->from('geobase__country')
                    ->orderBy('name')
                    ->indexBy('id')
                    ->column();
            
            return $countrys;
        }
        
        public static function getCitys() 
        {
            $citys = (new \yii\db\Query())
                    ->select(['name'])
                    ->from('geobase__city')
                    ->orderBy('name')
                    ->indexBy('id')
                    ->column();
            
            return $citys;
        }
        
        public static function getCityName($id) 
        {
            $city = (new \yii\db\Query())
                    ->select(['name'])
                    ->from('geobase__city')
                    ->where(['id' => $id])
                    ->one();
            
            return $city[name];
        }
        
        public static function getCountryName($id) 
        {
            $country = (new \yii\db\Query())
                    ->select(['name'])
                    ->from('geobase__country')
                    ->where(['id' => $id])
                    ->one();
            
            return $country[name];
        }
            
    }