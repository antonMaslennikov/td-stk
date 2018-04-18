<?php

namespace backend\models;

use Yii;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
    
/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property int $type
 * @property int $parent
 * @property string $direction
 * @property int $name
 * @property int $number
 * @property string $date
 * @property int $order_id
 * @property int $client_id
 * @property int $sum
 * @property int $sum_payed
 * @property int $payed
 * @property string $payment_type
 */
class Document extends \yii\db\ActiveRecord
{
    const TYPE_BILL = 1;
    const TYPE_AKT  = 2;
    const TYPE_NAKL = 3;
    
    const FOR_SUP = 0;
    const FOR_CLIENT = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }
    
    public static function getTypes()
    {
        return [
            self::TYPE_BILL => 'Счёт',
            self::TYPE_AKT  => 'Акт',
            self::TYPE_NAKL => 'Накладная',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'number', 'client_id'], 'required'],
            [['order_id'], 'integer'],
            ['type', 'default', 'value' => Document::TYPE_BILL],
            ['direction', 'default', 'value' => Document::FOR_CLIENT],
            [['date', 'type', 'direction','payment_type', 'sum', 'positions'], 'safe'],
            [['name', 'number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'parent' => 'Parent',
            'direction' => 'Direction',
            'name' => 'Название',
            'number' => 'Номер',
            'date' => 'Дата',
            'order_id' => 'Заказ',
            'sum' => 'Сумма',
            'sum_payed' => 'Сумма оплачено',
            'payed' => 'Оплачено',
            'payment_type' => 'Тип оплаты',
        ];
    }
    
    public function getPositions(){
        return $this->hasMany(DocumentPosition::className(), ['document_id' => 'id']);
    }
    
    
    public function download()
    {
        switch ($this->type)
        {
            case self::TYPE_BILL:
                $this->getBill();
                break;
                
            case self::TYPE_AKT:
                $this->getAkt();
                break;
                
            case self::TYPE_NAKL:
                $this->getNakl();
                break;
                
            default:
                throw new \Exception('Не известный тип документа');
                break;
        }   
    }
    
    protected function getBill()
    {
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(5);
        
        $row = 2;
        
        $sheet->setCellValue('A' . $row, self::$rekv[0]['org']);
        $sheet->getStyle('A' . $row)->applyFromArray(['font' => ['bold' => true]]);
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $row++;
        $sheet->setCellValue('A' . $row, self::$rekv[0]['address']);
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'ИНН ' . self::$rekv[0]['inn']);
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, 'КПП ' . self::$rekv[0]['kpp']);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('A' . $row . ':A' . ($row + 4))->applyFromArray(['borders' => ['left' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Получатель');
        $sheet->setCellValue('G' . $row, 'Сч. №');
        $sheet->setCellValue('H' . $row, self::$rekv[0]['rs']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
        $sheet->mergeCells('H' . $row . ':J' . ($row + 1));
        $sheet->getStyle('G' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,'vertical' => Alignment::VERTICAL_CENTER,]]);
        $sheet->getStyle('H' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_CENTER,]]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row . ':G' . ($row + 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('H' . $row . ':J' . ($row + 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, self::$rekv[0]['org']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Банк получателя');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'БИК');
        $sheet->setCellValue('H' . $row, self::$rekv[0]['bik']);
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->getStyle('G' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,]]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('H' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, self::$rekv[0]['bank']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'Сч. №');
        $sheet->setCellValue('H' . $row, self::$rekv[0]['ks']);
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->getStyle('G' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,]]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('H' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->getRowDimension($row)->setRowHeight(36);
        $sheet->setCellValue('A' . $row, $this->name . ' от ' . \Yii::$app->formatter->asDate($this->date));
        $sheet->getStyle('A' . $row)->applyFromArray(
            ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER,]]
        );
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Плательщик');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, "Бла-бла-бла реквизиты клиента");
        $sheet->mergeCells('C' . $row . ':J' . $row);
        
        $row += 2;
        
        $th = $tdc = $tdl = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER,],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],
        ];
        $tdl['alignment']['horizontal'] = Alignment::HORIZONTAL_LEFT;
        $th['font'] = ['bold' => true];
            
        $sheet->setCellValue('A' . $row, '№');
        $sheet->setCellValue('B' . $row, 'Наименование товара, работ, услуг');
        $sheet->setCellValue('G' . $row, 'Кол-во');
        $sheet->setCellValue('H' . $row, 'Цена');
        $sheet->setCellValue('I' . $row, 'Сумма');
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($th);
        $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($th);
        $sheet->getStyle('G' . $row)->applyFromArray($th);
        $sheet->getStyle('H' . $row)->applyFromArray($th);
        $sheet->getStyle('I' . $row . ':J' . $row)->applyFromArray($th);
        $sheet->getRowDimension($row)->setRowHeight(20);
        
        $row++;
        
        foreach ($this->positions AS $k => $p)
        {
            $sheet->setCellValue('A' . $row, $k + 1);
            $sheet->setCellValue('B' . $row, $p->name);
            $sheet->setCellValue('G' . $row, $p->quantity);
            $sheet->setCellValue('H' . $row, $p->price . ' р.');
            $sheet->setCellValue('I' . $row, ($p->price * $p->quantity) . ' р.');
            $sheet->mergeCells('B' . $row . ':F' . $row);
            $sheet->mergeCells('I' . $row . ':J' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($tdc);
            $sheet->getStyle('B' . $row . ':F' . $row)->applyFromArray($tdl);
            $sheet->getStyle('G' . $row)->applyFromArray($tdc);
            $sheet->getStyle('H' . $row)->applyFromArray($tdc);
            $sheet->getStyle('I' . $row . ':J' . $row)->applyFromArray($tdc);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }
        
        $styles = [
            'center' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'bold_right' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'font' => ['bold' => true]
            ],
        ];
        
        $sheet->setCellValue('A' . $row, 'Итого:');
        $sheet->setCellValue('I' . $row, $this->sum . ' р.');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styles['bold_right']);
        $sheet->getStyle('I' . $row)->applyFromArray($styles['center']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'В том числе НДС:');
        $sheet->setCellValue('I' . $row, 'Без НДС');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styles['bold_right']);
        $sheet->getStyle('I' . $row)->applyFromArray($styles['center']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Всего к оплате:');
        $sheet->setCellValue('I' . $row, $this->sum . ' р.');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($styles['bold_right']);
        $sheet->getStyle('I' . $row)->applyFromArray($styles['center']);
        
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Всего наименований ' . count($this->positions) . ', на сумму ' . $this->sum . ' р.');								
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        $row += 3;
        
        $sheet->setCellValue('A' . $row, 'директор');
        $sheet->setCellValue('H' . $row, '/' . self::$rekv[0]['dir'] . '/');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->getStyle('H' . $row)->applyFromArray($styles['center']);
        
        $row++;
        
        $sheet->setCellValue('E' . $row, 'подпись');
        $sheet->setCellValue('H' . $row, 'расшифровка подписи');
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray(array_merge($styles['center'], ['font' => ['size' => 7], 'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]]));
        $sheet->getStyle('H' . $row . ':J' . $row)->applyFromArray(array_merge($styles['center'], ['font' => ['size' => 7], 'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]]));
        $sheet->getRowDimension($row)->setRowHeight(10);
        
        $row += 3;
        $sheet->setCellValue('H' . $row, 'М.П.');
        
        $f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->name  . '.xlsx';
                        
        $writer = new Xlsx($spreadsheet);
        $writer->save($f);

        //printr($f);
        
        file_force_download($f);
        unlink($f);
    }
    
    protected function getAkt()
    {
        
    }
    
    protected function getNakl()
    {
        
    }
}
