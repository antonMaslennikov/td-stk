<?php
namespace backend\models;

use yii\base\Model;
use common\models\Product;
use yii\helpers\FileHelper;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Signup form
 */
class ProductImportForm extends Product
{
	/**
     * @var UploadedFile
     */
    public $file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['file', 'required'],
			['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['xlsx'], 'checkExtensionByMimeType' => false],

        ];
    }
	
	public function attributeLabels()
    {
        return [
            'file' => 'Файл (xlsx)',
        ];
    }
	
	public function import()
    {
		if (!$this->validate()) {
            return false;
        }
		
		$path = 'uploads/' . date('Y/m/d/') . $this->file->baseName . '.' . $this->file->extension;
		
		FileHelper::createDirectory(dirname($path));
		
		$this->file->saveAs($path);
		
		$reader = new Xlsx();
            
		$spreadsheet = $reader->load($path);

		$cells = $spreadsheet->getActiveSheet()->getCellCollection();
				 
		for ($row = 2; $row <= $cells->getHighestRow(); $row++){
			print_r($cells->get('A'.$row)->getValue());
		}   
		
		
		return true;
	}
}
