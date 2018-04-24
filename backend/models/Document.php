<?php

namespace backend\models;

use Yii;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use common\models\OrderClient;
    
/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property int $type
 * @property int $parent_id
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
    public $quantity;
    public $order_status;
    public $manager;
    
    const TYPE_BILL = 1;
    const TYPE_AKT  = 2;
    const TYPE_NAKL = 3;
    
    const FOR_SUP = 0;
    const FOR_CLIENT = 1;
    
    const PT_CASH = 0;
    const PT_CARD = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }
    
    public static function getPaymentTypes()
    {
        return [
            self::PT_CASH => 'Наличные',
            self::PT_CARD  => 'Наличные на карту',
        ];
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
            [['order_id', 'manager_id', 'nds_percent'], 'integer'],
            ['type', 'default', 'value' => Document::TYPE_BILL],
            ['direction', 'default', 'value' => Document::FOR_CLIENT],
            [['date', 'type', 'direction','payment_type', 'sum', 'positions', 'quantity', 'order_status', 'manager'], 'safe'],
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
            'client_id' => 'Клиент',
            'quantity' => 'Количество',
            'sum' => 'Сумма',
            'sum_payed' => 'Сумма оплачено',
            'payed' => 'Оплачено',
            'payment_type' => 'Тип оплаты',
            'manager' => 'Менеджер',
        ];
    }
    
    public function getPositions(){
        return $this->hasMany(DocumentPosition::className(), ['document_id' => 'id']);
    }
    
    public function getClient(){
        return $this->hasOne(OrderClient::className(), ['id' => 'client_id']);
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->manager_id = \Yii::$app->user->identity->getId();
            }

            return true;
        }
        return false;
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
    
    /**
     * Получить дочерние документы
     */
    public function getChildrens()
    {
        $childrens = Document::find()->where(['parent_id' => $this->id])->indexBy('type')->all();
        
        return $childrens;
    }
    
    public function getManagerList()
    {
        $rs = (new \yii\db\Query())
                        ->select(['u.id', 'u.fio', 'u.username'])
                        ->from(Document::tableName() . ' d' . ', ' . \common\models\User::tableName() . 'u')
                        ->where(['d.manager_id' => 'u.id'])
                        ->all();
        
        $managers = [];
        
        foreach ($rs AS $m) {
            $managers[$m['id']] = $m['fio'] ? $m['fio'] : $m['username'];
        }
        
        return $managers;
    }
    
    protected function getBill()
    {
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $rekv = OrderClient::find()->where(['id' => 1])->asArray()->one();
        
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(5);
        
        $row = 2;
        
        $sheet->setCellValue('A' . $row, $rekv['org']);
        $sheet->getStyle('A' . $row)->applyFromArray(['font' => ['bold' => true]]);
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $row++;
        $sheet->setCellValue('A' . $row, $rekv['address']);
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'ИНН ' . $rekv['inn']);
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, 'КПП ' . $rekv['kpp']);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':J' . $row);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('A' . $row . ':A' . ($row + 4))->applyFromArray(['borders' => ['left' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Получатель');
        $sheet->setCellValue('G' . $row, 'Сч. №');
        $sheet->setCellValue('H' . $row, $rekv['rs']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
        $sheet->mergeCells('H' . $row . ':J' . ($row + 1));
        $sheet->getStyle('G' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,'vertical' => Alignment::VERTICAL_CENTER,]]);
        $sheet->getStyle('H' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_CENTER,]]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row . ':G' . ($row + 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('H' . $row . ':J' . ($row + 1))->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, $rekv['org']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Банк получателя');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'БИК');
        $sheet->setCellValue('H' . $row, $rekv['bik']);
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->getStyle('G' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,]]);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(['borders' => ['top' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('G' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        $sheet->getStyle('H' . $row . ':J' . $row)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN,],],]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, $rekv['bank']);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'Сч. №');
        $sheet->setCellValue('H' . $row, $rekv['ks']);
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
        $sheet->setCellValue('C' . $row, $this->client->org . ', ' . $this->client->address . ',  ИНН ' . $this->client->inn . ', КПП '  . $this->client->inn . ', р/сч ' . $this->client->rs . ', банк ' . $this->client->bank . ', корр.счет ' . $this->client->ks . ', БИК ' . $this->client->bik);
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
        $sheet->setCellValue('H' . $row, '/' . $rekv['dir'] . '/');
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
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $rekv = OrderClient::find()->where(['id' => 1])->one();
        $bill = Document::findOne($this->parent_id);
            
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->getColumnDimension('A')->setWidth(5);
        
        $row = 2;
        
        $sheet->setCellValue('A' . $row, $this->name . ' от ' . \Yii::$app->formatter->asDate($this->date));
        $sheet->getStyle('A' . $row)->applyFromArray(
            ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER,]]
        );
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $sheet->getRowDimension($row)->setRowHeight(30);
        
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Исполнитель:');
        $sheet->setCellValue('C' . $row, $rekv->org . ', ' . $rekv->address . ',  ИНН ' . $rekv->inn . ",\n КПП "  . $rekv->inn . ', р/сч ' . $rekv->rs . ",\n банк " . $rekv->bank . ', корр.счет ' . $rekv->ks . ', БИК ' . $rekv->bik);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->mergeCells('C' . $row . ':J' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_TOP,]]);
        $sheet->getStyle('C' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_TOP,]]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Заказчик:');
        $sheet->setCellValue('C' . $row, $this->client->org . ', ' . $this->client->address . ", ИНН " . $this->client->inn . ",\n КПП "  . $this->client->inn . ', р/сч ' . $this->client->rs . ",\n банк " . $this->client->bank . ', корр.счет ' . $this->client->ks . ', БИК ' . $this->client->bik);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->mergeCells('C' . $row . ':J' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_TOP,]]);
        $sheet->getStyle('C' . $row)->applyFromArray(['alignment' => ['vertical' => Alignment::VERTICAL_TOP,]]);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'По счёту:');
        $sheet->setCellValue('C' . $row, 'Счёт №' . $bill->number);
        $sheet->mergeCells('A' . $row . ':B' . $row);
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
        
        $sheet->setCellValue('A' . $row, 'Всего оказано услуг на сумму:');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, num_propis($this->sum) . ' руб.');
        $sheet->mergeCells('E' . $row . ':J' . $row);
        $sheet->getStyle('E' . $row)->applyFromArray(['font' => ['bold' => true]]);
        
        $row++;
        $sheet->setCellValue('A' . $row, "Вышеперечисленные услуги выполнены полностью и в срок.\nЗаказчик претензий по объему, качеству и срокам оказания услуг не имеет.");	$sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(32);
        
        $sheet->mergeCells('A' . $row . ':J' . $row);

        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Исполнитель');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, 'Заказчик');
        $sheet->mergeCells('G' . $row . ':J' . $row);
        $sheet->getRowDimension($row)->setRowHeight(35);
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray(
            ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER,]]
        );
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'директор');
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        $row += 4;
        
        $sheet->setCellValue('D' . $row, '/' . $rekv->dir . '/');
        $sheet->mergeCells('D' . $row . ':E' . $row);
        $sheet->getStyle('D' . $row)->applyFromArray($styles['center']);
        
        $row++;
        
        $sheet->setCellValue('B' . $row, 'подпись');
        $sheet->setCellValue('D' . $row, 'расшифровка подписи');
        $sheet->mergeCells('B' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':E' . $row);
        $sheet->getStyle('B' . $row . ':E' . $row)->applyFromArray(array_merge($styles['center'], ['font' => ['size' => 7], 'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]]));
        $sheet->setCellValue('G' . $row, 'подпись');
        $sheet->setCellValue('I' . $row, 'расшифровка подписи');
        $sheet->mergeCells('G' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->getStyle('G' . $row . ':J' . $row)->applyFromArray(array_merge($styles['center'], ['font' => ['size' => 7], 'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]]));
        
        
        $sheet->getRowDimension($row)->setRowHeight(10);
        
        
        $f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->name  . '.xlsx';
                        
        $writer = new Xlsx($spreadsheet);
        $writer->save($f);
        
        file_force_download($f);
        unlink($f);
    }
    
    protected function getNakl()
    {
        \Yii::$app->formatter->locale = 'ru-RU';
        
        $rekv = OrderClient::find()->where(['id' => 1])->one();
        $bill = Document::findOne($this->parent_id);
        
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->getPageSetup()
                    ->setFitToPage(true)
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            
        $sheet->getPageMargins()->setHeader(0.5);
        $sheet->getPageMargins()->setTop(1);
        $sheet->getPageMargins()->setBottom(1);
        $sheet->getPageMargins()->setRight(1);
        $sheet->getPageMargins()->setLeft(1);
        
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
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            'td_l' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER], 
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]],
            'underline' => [
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'podp' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP]],
        ];
        
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        
        $sheet->getDefaultRowDimension()->setRowHeight(12);
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(6);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('G')->setWidth(9);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(7);
        $sheet->getColumnDimension('J')->setWidth(3);
        $sheet->getColumnDimension('K')->setWidth(3);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(11);
        $sheet->getColumnDimension('N')->setWidth(8);
        $sheet->getColumnDimension('O')->setWidth(10);
        $sheet->getColumnDimension('P')->setWidth(9);
        $sheet->getColumnDimension('Q')->setWidth(8);
        $sheet->getColumnDimension('R')->setWidth(9);
                
        $row = 1;    
            
        $sheet->setCellValue('A' . $row, 'Унифицированная форма № ТОРГ-12');
        $sheet->getStyle('A' . $row)->applyFromArray($styles['small'] + $styles['h_align_r']);
        $sheet->mergeCells('A' . $row . ':R' . $row);
        $sheet->getRowDimension($row)->setRowHeight(10);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Утверждена постановлением Госкомстата России от 25.12.98 № 132');
        $sheet->getStyle('A' . $row)->applyFromArray($styles['small'] + $styles['h_align_r']);
        $sheet->mergeCells('A' . $row . ':R' . $row);
        $sheet->getRowDimension($row)->setRowHeight(10);
        
        $row++;
        
        $sheet->setCellValue('R' . $row, 'КОД');
        $sheet->getStyle('R' . $row)->applyFromArray($styles['h_align_c']);
        
        $row++;
        
        $sheet->setCellValue('P' . $row, 'Форма по ОКУД');
        $sheet->setCellValue('R' . $row, '330212');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Грузоотправитель');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $rekv->org . ', ' . $rekv->address . ',  ИНН ' . $rekv->inn . ", КПП "  . $rekv->inn . ', р/сч ' . $rekv->rs . ",\n банк " . $rekv->bank . ', корр.счет ' . $rekv->ks . ', БИК ' . $rekv->bik);
        $sheet->mergeCells('C' . $row . ':O' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(23);
        $sheet->setCellValue('P' . $row, 'по ОКПО');
        $sheet->setCellValue('R' . $row, '83132124');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('C' . $row . ':O' . $row)->applyFromArray($styles['border_b']);
        	
        $row++;
        
        $sheet->setCellValue('L' . $row, 'Вид деятельности по ОКДП');
        $sheet->mergeCells('L' . $row . ':Q' . $row);
        $sheet->getStyle('L' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Грузополучатель');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $bill->client->org . ', ' . $bill->client->address . ',  ИНН ' . $bill->client->inn . ", КПП "  . $bill->client->inn . ', р/сч ' . $bill->client->rs . ",\n банк " . $bill->client->bank . ', корр.счет ' . $bill->client->ks . ', БИК ' . $bill->client->bik);
        $sheet->mergeCells('C' . $row . ':O' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(23);
        $sheet->setCellValue('P' . $row, 'по ОКПО');
        $sheet->setCellValue('R' . $row, '');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('C' . $row . ':O' . $row)->applyFromArray($styles['border_b']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Поставщик');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $rekv->org . ', ' . $rekv->address . ',  ИНН ' . $rekv->inn . ", КПП "  . $rekv->inn . ', р/сч ' . $rekv->rs . ",\n банк " . $rekv->bank . ', корр.счет ' . $rekv->ks . ', БИК ' . $rekv->bik);
        $sheet->mergeCells('C' . $row . ':O' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(23);
        $sheet->setCellValue('P' . $row, 'по ОКПО');
        $sheet->setCellValue('R' . $row, '83132124');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('C' . $row . ':O' . $row)->applyFromArray($styles['border_b']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Плательщик');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $bill->client->org . ', ' . $bill->client->address . ',  ИНН ' . $bill->client->inn . ", КПП "  . $bill->client->inn . ', р/сч ' . $bill->client->rs . ",\n банк " . $bill->client->bank . ', корр.счет ' . $bill->client->ks . ', БИК ' . $bill->client->bik);
        $sheet->mergeCells('C' . $row . ':O' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(23);
        $sheet->setCellValue('P' . $row, 'по ОКПО');
        $sheet->setCellValue('R' . $row, '');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('C' . $row . ':O' . $row)->applyFromArray($styles['border_b']);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Основание');
        $sheet->setCellValue('C' . $row, 'Счёт №' . $bill->number);
        $sheet->setCellValue('P' . $row, 'номер');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->mergeCells('C' . $row . ':O' . $row);
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o']);
        
        $row++;
        $sheet->setCellValue('P' . $row, 'дата');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o']);
        
        $row++;
        $sheet->setCellValue('P' . $row, 'номер');
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o']);
        
        $row++;
        $sheet->setCellValue('F' . $row, 'Номер документа');
        $sheet->setCellValue('H' . $row, 'Дата составления');
        $sheet->setCellValue('P' . $row, 'дата');
        $sheet->mergeCells('F' . $row . ':G' . $row);
        $sheet->mergeCells('H' . $row . ':K' . $row);
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('F' . $row . ':G' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('H' . $row . ':K' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o']);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_r']);
        
        
        $row++;
        $sheet->setCellValue('C' . $row, 'ТОВАРНАЯ НАКЛАДНАЯ');
        $sheet->setCellValue('F' . $row, $this->number);
        $sheet->setCellValue('H' . $row, \Yii::$app->formatter->asDate($this->date));
        $sheet->setCellValue('P' . $row, 'вид операции');
        $sheet->mergeCells('C' . $row . ':E' . $row);
        $sheet->mergeCells('F' . $row . ':G' . $row);
        $sheet->mergeCells('H' . $row . ':K' . $row);
        $sheet->mergeCells('P' . $row . ':Q' . $row);
        $sheet->getStyle('C' . $row . ':E' . $row)->applyFromArray($styles['bold'] + $styles['h_align_r']);
        $sheet->getStyle('F' . $row . ':G' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('H' . $row . ':K' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_c']);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['border_o'] + $styles['h_align_r']);
        $sheet->getStyle('R' . $row)->applyFromArray($styles['border_o']);
        
        $row++;
        $sheet->getRowDimension($row)->setRowHeight(4);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, "Номер\nпо\nпорядку");
        $sheet->setCellValue('B' . $row, 'Товар');
        $sheet->setCellValue('E' . $row, 'Единица измерения');
        $sheet->setCellValue('G' . $row, "Вид\nупаковки");
        $sheet->setCellValue('H' . $row, 'Количество');
        $sheet->setCellValue('K' . $row, 'Масса брутто');
        $sheet->setCellValue('M' . $row, "Количество\n(масса нетто)");
        $sheet->setCellValue('N' . $row, "Цена,\nруб.коп.");
        $sheet->setCellValue('O' . $row, "Сумма без\nучета НДС,\nруб.коп.");
        $sheet->setCellValue('P' . $row, 'НДС');
        $sheet->setCellValue('R' . $row, "Сумма без\nучета НДС,\nруб.коп.");
        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
        $sheet->mergeCells('H' . $row . ':J' . $row);
        $sheet->mergeCells('K' . $row . ':L' . ($row + 1));
        $sheet->mergeCells('M' . $row . ':M' . ($row + 1));
        $sheet->mergeCells('N' . $row . ':N' . ($row + 1));
        $sheet->mergeCells('O' . $row . ':O' . ($row + 1));
        $sheet->mergeCells('P' . $row . ':Q' . $row);   
        $sheet->mergeCells('R' . $row . ':R' . ($row + 1));   
        $sheet->getRowDimension($row)->setRowHeight(24);
        $sheet->getStyle('A' . $row . ':R' . ($row + 2))->applyFromArray($styles['th']);
        $sheet->getStyle('A' . $row . ':R' . ($row + 2))->getAlignment()->setWrapText(true);
        
        $row++;
        
        $sheet->setCellValue('B' . $row, "наименование, характеристика, сорт,\nартикул товара");
        $sheet->setCellValue('D' . $row, 'код');
        $sheet->setCellValue('E' . $row, "наиме-\nнование");
        $sheet->setCellValue('F' . $row, 'код по ОКЕИ');
        $sheet->setCellValue('H' . $row, 'в одном месте');
        $sheet->setCellValue('I' . $row, 'мест, штук');
        $sheet->setCellValue('P' . $row, 'ставка, %');
        $sheet->setCellValue('Q' . $row, "сумма,\nруб.коп.");
        $sheet->mergeCells('B' . $row . ':C' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
            
        $row++;
        
        $sheet->setCellValue('A' . $row, 1);
        $sheet->setCellValue('B' . $row, 2);
        $sheet->setCellValue('D' . $row, 3);
        $sheet->setCellValue('E' . $row, 4);
        $sheet->setCellValue('F' . $row, 5);
        $sheet->setCellValue('G' . $row, 6);
        $sheet->setCellValue('H' . $row, 7);
        $sheet->setCellValue('I' . $row, 8);
        $sheet->setCellValue('K' . $row, 9);
        $sheet->setCellValue('M' . $row, 10);
        $sheet->setCellValue('N' . $row, 11);
        $sheet->setCellValue('O' . $row, 12);
        $sheet->setCellValue('P' . $row, 13);
        $sheet->setCellValue('Q' . $row, 14);
        $sheet->setCellValue('R' . $row, 15);
        $sheet->mergeCells('B' . $row . ':C' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->mergeCells('K' . $row . ':L' . $row);
        
        $sheet->getStyle('A' . $row . ':R' . ($row + count($this->positions) - 1))->applyFromArray($styles['th']);
        $sheet->getStyle('B' . $row . ':B' . ($row + count($this->positions) - 1))->applyFromArray($styles['td_l']);
        
        foreach ($this->positions as $i => $g)
        {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $g['name']);
            $sheet->setCellValue('D' . $row, '-');
            $sheet->setCellValue('E' . $row, 'шт');
            $sheet->setCellValue('F' . $row, '796');
            $sheet->setCellValue('G' . $row, '-');
            $sheet->setCellValue('H' . $row, '-');
            $sheet->setCellValue('I' . $row, '-');
            $sheet->setCellValue('K' . $row, '-');
            $sheet->setCellValue('M' . $row, $g['quantity']);
            $sheet->setCellValue('N' . $row, $g['price']);
            $sheet->setCellValue('O' . $row, number_format($g['quantity'] * $g['price'], 2, ',', ' '));
            $sheet->setCellValue('P' . $row, $this->nds_percent > 0 ? $this->nds_percent : 'Без НДС');
            $sheet->setCellValue('Q' . $row, $this->nds_percent > 0 ? number_format(round($g['quantity'] * $g['price'] * ($this->nds_percent / 100) / (1 + $this->nds_percent / 100), 2), 2, ',', ' ') : 'Без НДС');
            $sheet->setCellValue('R' . $row, number_format($g['quantity'] * $g['price'], 2, ',', ' '));

            $sheet->mergeCells('B' . $row . ':C' . $row);
            $sheet->mergeCells('I' . $row . ':J' . $row);
            $sheet->mergeCells('K' . $row . ':L' . $row);
            
            $i++;
            $row++; 
            $tquantity += $g['quantity'];
            $tprice += $g['quantity'] * $g['price'];
        }
        
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->fromArray(['X', '', 'X', '', $tquantity, 'X', number_format($this->sum, 2, ',', ' '), ($this->nds_percent > 0 ? $this->nds_percent : 'Без НДС'), ($this->nds_percent > 0 ? number_format($this->nds, 2, ',', ' ') : 'Без НДС'), number_format($this->sum, 2, ',', ' ')], null, 'I' . $row);
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->mergeCells('K' . $row . ':L' . $row);
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('I' . $row . ':R' . $row)->applyFromArray($styles['th']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Всего по накладной');
        $sheet->fromArray(['X', '', 'X', '', $tquantity, 'X', number_format($this->sum, 2, ',', ' '), ($this->nds_percent > 0 ? $this->nds_percent : 'Без НДС'), ($this->nds_percent > 0 ? number_format($this->nds, 2, ',', ' ') : 'Без НДС'), number_format($this->sum, 2, ',', ' ')], null, 'I' . $row);
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':J' . $row);
        $sheet->mergeCells('K' . $row . ':L' . $row);
        $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($styles['h_align_r']);
        $sheet->getStyle('I' . $row . ':R' . $row)->applyFromArray($styles['th']);
        
        $row++;
        
        
        
        $sheet->getRowDimension($row)->setRowHeight(7);
        
        $row++;
        
        $sheet->setCellValue('B' . $row, 'Товарная накладная имеет приложения на');
        $sheet->setCellValue('D' . $row, 'один');
        $sheet->setCellValue('O' . $row, 'листах');
        $sheet->mergeCells('B' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':N' . $row);
        $sheet->mergeCells('O' . $row . ':R' . $row);
        $sheet->getStyle('D' . $row . ':N' . $row)->applyFromArray($styles['underline']);
        
        $row++;

        $sheet->setCellValue('B' . $row, 'и содержит');
        $sheet->setCellValue('D' . $row, 'один');									
        $sheet->setCellValue('O' . $row, 'порядковых номеров записей');
        $sheet->mergeCells('B' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':N' . $row);
        $sheet->mergeCells('O' . $row . ':R' . $row);
        $sheet->getStyle('D' . $row . ':N' . $row)->applyFromArray($styles['underline']);
            
        $row++;
        
        $sheet->setCellValue('D' . $row, '(прописью)');
        $sheet->mergeCells('D' . $row . ':N' . $row);
        $sheet->getStyle('D' . $row . ':N' . $row)->applyFromArray($styles['podp']);
        
        $row++;
        
        $sheet->setCellValue('G' . $row, 'Масса груза (нетто)');
        $sheet->mergeCells('G' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':O' . $row);
        $sheet->mergeCells('P' . $row . ':R' . $row);
        $sheet->getStyle('P' . $row . ':R' . $row)->applyFromArray($styles['border_o']);
        $sheet->getStyle('I' . $row . ':N' . $row)->applyFromArray($styles['underline']);
        
        $row ++;
        $sheet->setCellValue('I' . $row, '(прописью)');
        $sheet->mergeCells('I' . $row . ':N' . $row);
        $sheet->getStyle('I' . $row . ':N' . $row)->applyFromArray($styles['podp']);
        $sheet->getRowDimension($row)->setRowHeight(12);
        
        $row ++;
        
        $sheet->setCellValue('B' . $row, 'Всего мест');
        $sheet->setCellValue('C' . $row, 'один');
        $sheet->setCellValue('G' . $row, 'Масса груза (брутто)');
        $sheet->mergeCells('C' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':H' . $row);
        $sheet->mergeCells('I' . $row . ':N' . $row);
        $sheet->mergeCells('P' . $row . ':R' . $row);
        $sheet->getStyle('C' . $row . ':F' . $row)->applyFromArray($styles['underline']);
        $sheet->getStyle('I' . $row . ':N' . $row)->applyFromArray($styles['underline']);
        $sheet->getStyle('P' . $row . ':R' . $row)->applyFromArray($styles['border_o']);

        $row ++;
        
        $sheet->setCellValue('C' . $row, '(прописью)');
        $sheet->setCellValue('I' . $row, '(прописью)');
        $sheet->mergeCells('C' . $row . ':F' . $row);
        $sheet->mergeCells('I' . $row . ':N' . $row);
        $sheet->getStyle('C' . $row . ':F' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('I' . $row . ':N' . $row)->applyFromArray($styles['podp']);
        $sheet->getRowDimension($row)->setRowHeight(11);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Приложение (паспорты, сертификаты) на');
        $sheet->setCellValue('I' . $row, 'листах');
        $sheet->setCellValue('L' . $row, 'По доверенности №');
        $sheet->setCellValue('O' . $row, 'от');
        $sheet->setCellValue('R' . $row, 'года,');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('L' . $row . ':M' . $row);
        $sheet->mergeCells('D' . $row . ':H' . $row);
        $sheet->getStyle('O' . $row . ':O' . $row)->applyFromArray($styles['h_align_c']);
        $sheet->getStyle('D' . $row . ':H' . $row)->applyFromArray($styles['underline']);
        $sheet->getStyle('N' . $row . ':N' . $row)->applyFromArray($styles['underline']);
        $sheet->getStyle('P' . $row . ':Q' . $row)->applyFromArray($styles['underline']);
        
        $sheet->getStyle('J' . $row . ':J' . ($row + 10))->applyFromArray($styles['border_r']);
        
        $row++;
        
        $sheet->setCellValue('D' . $row, '(прописью)');
        $sheet->mergeCells('D' . $row . ':H' . $row);
        $sheet->getStyle('D' . $row . ':H' . $row)->applyFromArray($styles['podp']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Всего отпущенно на сумму');	
        $sheet->setCellValue('D' . $row, num_propis($this->sum) . ' руб. 00 копеек.');
        $sheet->setCellValue('L' . $row, 'выданной');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':I' . $row);
        $sheet->getStyle('D' . $row . ':I' . $row)->applyFromArray($styles['underline']);
        $sheet->mergeCells('M' . $row . ':R' . $row);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['underline']);
        
        $row++;
        
        $sheet->setCellValue('D' . $row, '(прописью)');
        $sheet->setCellValue('M' . $row, '(кем, кому (организация, место работы, должность, фамилия, и., о.))');
        $sheet->mergeCells('D' . $row . ':I' . $row);
        $sheet->getStyle('D' . $row . ':I' . $row)->applyFromArray($styles['podp']);
        $sheet->mergeCells('M' . $row . ':R' . $row);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['podp'] + $styles['underline']);
        $sheet->getStyle('L' . $row)->applyFromArray($styles['underline']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Отпуск разрешил');
        $sheet->setCellValue('C' . $row, 'директор');
        $sheet->setCellValue('G' . $row, $rekv->dir);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->mergeCells('L' . $row . ':R' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('L' . $row . ':R' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);

        $row++;

        $sheet->setCellValue('C' . $row, '(должность)');
        $sheet->setCellValue('E' . $row, '(подпись)');
        $sheet->setCellValue('G' . $row, '(расшифровка подписи)');
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->getStyle('C' . $row . ':C' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['podp']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Главный (старший) бухгалтер');
        $sheet->setCellValue('G' . $row, $rekv->dir);
        $sheet->setCellValue('L' . $row, 'Груз принял');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->mergeCells('M' . $row . ':R' . $row);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['underline']);
        
        $row++;
        
        $sheet->setCellValue('E' . $row, '(подпись)');
        $sheet->setCellValue('G' . $row, '(расшифровка подписи)');
        $sheet->setCellValue('M' . $row, '(должность)');
        $sheet->setCellValue('N' . $row, '(подпись)');
        $sheet->setCellValue('P' . $row, '(расшифровка подписи)');
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->mergeCells('N' . $row . ':O' . $row);
        $sheet->mergeCells('P' . $row . ':R' . $row);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['podp']);
                
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Отпуск разрешил');
        $sheet->setCellValue('C' . $row, 'директор');
        $sheet->setCellValue('G' . $row, $rekv->dir);
        $sheet->setCellValue('L' . $row, 'Груз получил');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->mergeCells('M' . $row . ':R' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['underline']);
        
        $row++;
        
        $sheet->setCellValue('C' . $row, '(должность)');
        $sheet->setCellValue('E' . $row, '(подпись)');
        $sheet->setCellValue('G' . $row, '(расшифровка подписи)');
        $sheet->setCellValue('M' . $row, '(должность)');
        $sheet->setCellValue('N' . $row, '(подпись)');
        $sheet->setCellValue('P' . $row, '(расшифровка подписи)');
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $sheet->mergeCells('N' . $row . ':O' . $row);
        $sheet->mergeCells('P' . $row . ':R' . $row);
        $sheet->getStyle('C' . $row . ':C' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($styles['podp']);
        $sheet->getStyle('M' . $row . ':R' . $row)->applyFromArray($styles['podp']);
        
        $row++;
        
        $sheet->setCellValue('A' . $row, 'М.П.');
        $sheet->setCellValue('E' . $row, \Yii::$app->formatter->asDate($this->date));
        $sheet->setCellValue('I' . $row, 'года');
        $sheet->setCellValue('L' . $row, 'М.П.');
        $sheet->setCellValue('R' . $row, 'года');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('E' . $row . ':H' . $row);
        $sheet->mergeCells('L' . $row . ':M' . $row);
        $sheet->mergeCells('N' . $row . ':Q' . $row);
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($styles['h_align_c']);
        $sheet->getStyle('E' . $row . ':H' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        $sheet->getStyle('L' . $row . ':M' . $row)->applyFromArray($styles['h_align_c']);
        $sheet->getStyle('N' . $row . ':Q' . $row)->applyFromArray($styles['h_align_c'] + $styles['underline']);
        
        
        $f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->name  . '.xlsx';
                        
        $writer = new Xlsx($spreadsheet);
        $writer->save($f);

        //printr($f);
        
        file_force_download($f);
        unlink($f);
    }
    
    public function pay($sum)
    {
        $this->sum_payed += $sum;
        if ($this->sum_payed >= $this->sum) {
            $this->payed = 1;
        }
        $this->save();
        
        return true;
    }
}
