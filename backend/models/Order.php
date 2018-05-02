<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

use backend\models\Document;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $client_id
 * @property int $address_id
 * @property int $payment_type
 * @property int $delivery_type
 * @property string $created_at
 */
class Order extends \common\models\Order
{
    public $manager;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(\common\models\Order::rules(), [
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(\common\models\Order::attributeLabels(), [
        ]);
    }
    
    public static function getPTList()
    {
        return ArrayHelper::getColumn(self::$paymentTypes, 'name');
    }
    
    public static function getDTList()
    {
        return ArrayHelper::getColumn(self::$deliveryTypes, 'name');
    }
    
    public function getClientBills(){
        return $this
                ->hasMany(Document::className(), ['order_id' => 'id'])
                ->where(['type' => Document::TYPE_BILL, 'direction' => Document::FOR_CLIENT]);
    }
    
    public function getLogsTimeline()
    {
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $team = \common\models\user::find()->indexBy('id')->all();
        
        $data = [];
        
        foreach ($this->logs AS $l)
        {
            $day = date('y-m-d', strtotime($l->time));
            
            if (!$data[$day]) {
                $data[$day] = ['date' => \Yii::$app->formatter->asDate($l->time)];
            }
            
            $data[$day]['rows'][] = [
                'user' => $team[$l->user_id],
                'action' => $l['action'],
                'result' => $l['result'],
                'info' => $l['info'],
                'time' => date('H:i', strtotime($l->time)),
            ];
        }
        
        //printr($data);
        
        return $data;
    }
    
    public function donwnloadApplication()
    {
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        
        $styles = [
            'small' => ['font' => ['size' => 7]],
            'bold' => ['font' => ['bold' => true]],
            'h_align_r' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER]],
            'h_align_c' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'border_o' => ['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            'border_b' => ['borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]],
            'border_r' => ['borders' => ['right' => ['borderStyle' => Border::BORDER_THIN]]],
            'th' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER], 
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
                
            'td_l' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER], 
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            'underline' => [
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'podp' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP]],
        ];
        
        $row = 2;
        
        $sheet->setCellValue('G' . $row, 'Массогабаритные характеристики товаров');
        $sheet->getStyle('G' . $row)->applyFromArray($styles['bold']);
        $sheet->mergeCells('G' . $row . ':L' . $row);
        
        $row++;
        
        $sheet->fromArray(["Внутренний\nкод\nпозиции", "Название\nтовара", "Вес брутто единицы товара, кг", "Ширина единицы товара, см", "Высота единицы товара, см", "Длина единицы товара, см", "Штук в блоке", "Вес брутто блока, кг", "Ширина блока, см", "Высота блока, см", "Длина блока, см", "Штук в коробке", "Вес брутто коробки, кг", "Ширина коробки, см", "Высота коробки, см", "Длина коробки, см", "Штук в слое", "Штук на поддоне"], null, 'A' . $row);
        $sheet->getRowDimension($row)->setRowHeight(60);
        $sheet->getStyle('A' . $row . ':R' . $row)->getAlignment()->setWrapText(true);;
        
        $row++;
        
        $sheet->fromArray(['', '', "Вес брутто в КГ", "Реальные габариты товар", "Реальные габариты товара", "Реальные габариты товара", "Количество шт в блоке", "Вес брутто блока в КГ", "Реальные габариты блока", "Реальные габариты блока", "Реальные габариты блока", "Количество шт в коробке", "Вес брутто коробки в КГ", "Реальные габариты коробки", "Реальные габариты коробки", "Реальные габариты коробки", "Количество шт в слое", "Количество шт в паллете"], null, 'A' . $row);
        $sheet->getRowDimension($row)->setRowHeight(90);
        $sheet->getStyle('A' . $row . ':R' . $row)->getAlignment()->setWrapText(true);;
        
        
        $row += 4;
        
        $sheet->setCellValue('A' . $row, 'Поставщик ________________________________________');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        
        $row += 4;
        
        $sheet->setCellValue('A' . $row, 'Покупатель ________________________________________');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        
        $f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Приложение к заказу №' . $this->id  . '.xlsx';
                        
        $writer = new Xlsx($spreadsheet);
        $writer->save($f);

        file_force_download($f);
        unlink($f);
    }
}
