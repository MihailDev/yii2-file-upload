<?php
/**
 * Date: 08.08.14
 * Time: 1:03
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\fileupload;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class FileUploadWidget
 *
 * @package mihaildev\fileupload
 */
class FileUploadWidget extends InputWidget{
	public $checkboxOptions = [];
	public $linkOptions = [];

	public $fileUrl;

	public $fileName = '';

	public $template = '{hiddenInput} {fileLink} {checkbox} {fileInput}';

	public $checkboxTemplate = '<label>{checkbox} {checkboxLabel}</label>';

	public function init(){
		parent::init();
		if(empty($this->fileUrl)){
			if($this->hasModel()){
				if($this->model->hasMethod('getUploadedFileUrl'))
					$this->fileUrl = $this->model->getUploadedFileUrl($this->attribute);
			}
		}

		if(empty($this->fileName))
			$this->fileName = $this->fileUrl;
	}

	public function run()
	{

		$inputName = Html::getInputName($this->model, $this->attribute);

		$replace['{hiddenInput}'] = Html::hiddenInput($inputName, '');

		if(!empty($this->fileUrl)){
			$replace['{fileLink}'] = Html::a($this->fileName, $this->fileUrl, $this->linkOptions);

			$this->checkboxOptions['value'] = FileUploadBehavior::DELETE_VALUE;

			$replace['{checkbox}'] = strtr($this->checkboxTemplate,[
				'{checkbox}' => Html::checkbox($inputName, false, $this->checkboxOptions),
				'{checkboxLabel}' => \Yii::t('yii', 'Delete')
			]);
		}else{
			$replace['{fileLink}'] = '';
			$replace['{checkbox}'] = '';
		}

		if ($this->hasModel()) {
			$replace['{fileInput}'] = Html::activeInput('file', $this->model, $this->attribute, $this->options);
		} else {
			$replace['{fileInput}'] = Html::input('file', $this->name, '', $this->options);
		}

		echo strtr($this->template, $replace);
	}
} 