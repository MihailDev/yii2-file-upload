<?php
/**
 * Date: 08.08.14
 * Time: 1:59
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\fileupload;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class ImageUploadWidget
 *
 * @package mihaildev\fileupload
 */
class ImageUploadWidget extends InputWidget{
	public $checkboxOptions = [];
	public $imageOptions = [];

	public $imageUrl;

	public $template = '{hiddenInput} {image} {checkbox} {fileInput}';

	public $checkboxTemplate = '<label>{checkbox} {checkboxLabel}</label>';

	public function init(){
		parent::init();
		if(empty($this->imageUrl)){
			if($this->hasModel()){
				if($this->model->hasMethod('getUploadedFileUrl'))
					$this->imageUrl = $this->model->getUploadedFileUrl($this->attribute);
			}
		}
	}

	public function run()
	{

		$inputName = Html::getInputName($this->model, $this->attribute);

		$replace['{hiddenInput}'] = Html::hiddenInput($inputName, '');

		if(!empty($this->imageUrl)){
			$replace['{image}'] = Html::img($this->imageUrl, $this->imageOptions);

			$this->checkboxOptions['value'] = FileUploadBehavior::DELETE_VALUE;

			$replace['{checkbox}'] = strtr($this->checkboxTemplate,[
				'{checkbox}' => Html::checkbox($inputName, false, $this->checkboxOptions),
				'{checkboxLabel}' => \Yii::t('yii', 'Delete')
			]);
		}else{
			$replace['{image}'] = '';
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