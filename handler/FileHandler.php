<?php
/**
 * Date: 07.08.14
 * Time: 18:50
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\fileupload\handler;
use mihaildev\fileupload\FileUploadBehavior;
use yii\base\Exception;
use yii\base\Object;
use yii\web\UploadedFile;

/**
 * Class BasePlugin
 *
 * @package mihaildev\fileupload\plugin
 */
class FileHandler extends BaseHandler{
	/**
	 * @var UploadedFile
	 */
	protected $_file;

	protected $_beforeValidateAttributeValue;

	protected $_deletePaths;

	/**
	 * @inheritdoc
	 */
	public function beforeValidate($oldModel){
		$this->_beforeValidateAttributeValue = $this->behavior->owner->{$this->attribute};

		$this->_file = UploadedFile::getInstance($this->behavior->owner, $this->attribute);

		if ($this->_file instanceof UploadedFile) {
			$this->behavior->owner->{$this->attribute} = $this->_file;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($oldModel){
		if ($this->_file instanceof UploadedFile) {
			if(!empty($oldModel))
				$this->deleteFiles($oldModel);

			$this->behavior->owner->{$this->attribute} = $this->_file->baseName . '.' . $this->_file->extension;
		}else{
			if($this->_beforeValidateAttributeValue === FileUploadBehavior::DELETE_VALUE || empty($oldModel)){
				if(!empty($oldModel))
					$this->deleteFiles($oldModel);

				$this->behavior->owner->{$this->attribute} = '';
			}else{
				$this->behavior->owner->{$this->attribute} = $oldModel->{$this->attribute};
			}
		}
	}

	/**
	 * @param $model FileUploadBehavior
	 */
	protected function deleteFiles($model){
		$this->_deletePaths[] = $model->getUploadedFilePath($this->attribute);
	}

	protected function processDeleteFiles(){
		if(empty($this->_deletePaths))
			return;
		$this->_deletePaths = array_unique($this->_deletePaths);
		foreach($this->_deletePaths as $path){
			@unlink($path);
		}
	}

	/**
	 * @param $oldModel FileUploadBehavior
	 * @throws \yii\base\Exception
	 */
	public function afterSave($oldModel){
		$this->processDeleteFiles();

		if ($this->_file instanceof UploadedFile) {
			$path = $this->getFilePath();
			@mkdir(pathinfo($path, PATHINFO_DIRNAME), 0777, true);
			if (!$this->_file->saveAs($path)) {
				throw new Exception('File saving error. Path: '.$path);
			}
		}else{
			$this->renew($oldModel);
		}
	}

	protected function getPath($attributePath, $attributeName, $attributeValue){

		$pathInfo = pathinfo($attributeValue);
		$pathInfo['extension'] = strtolower($pathInfo['extension']);

		if(is_string($attributePath)){
			$path = $attributePath;

			$replacePairs = [
				'<fileName>' => $pathInfo['filename'],
				'<fileExtension>' => $pathInfo['extension'],
				'<attributeName>' => $attributeName,
			];

			foreach($this->behavior->replacePairs as $key=>$keyAttribute){
				$replacePairs[$key] = $this->behavior->owner->{$keyAttribute};
			}

			$path = strtr($path, $replacePairs);
		}else{
			$path = call_user_func($attributePath, $this->behavior, $this->behavior->owner, $pathInfo['extension'], $pathInfo['filename'], $attributeName);
		}

		return \Yii::getAlias($path);
	}

	protected $_filePath;

	/**
	 * @inheritdoc
	 */
	public function getFilePath()
	{
		if(!empty($this->_filePath))
			return $this->_filePath;

		if(empty($this->behavior->owner->{$this->attribute}))
			return null;

		$this->_filePath =  $this->getPath($this->options['path'], $this->attribute, $this->behavior->owner->{$this->attribute});

		return $this->_filePath;
	}

	protected $_fileUrl;

	/**
	 * @inheritdoc
	 */
	public function getFileUrl()
	{
		if(!empty($this->_fileUrl))
			return $this->_fileUrl;

		if(empty($this->behavior->owner->{$this->attribute}))
			return "";

		$this->_fileUrl = $this->getPath($this->options['url'], $this->attribute, $this->behavior->owner->{$this->attribute});

		return $this->_fileUrl;
	}

	/**
	 * @inheritdoc
	 */
	public function delete(){
		$this->deleteFiles($this->behavior->owner);
		$this->processDeleteFiles();
	}

	/**
	 * @param $oldModel FileUploadBehavior
	 */
	public function renew($oldModel){
		if(empty($oldModel))
			return;

		$oldPath = $oldModel->getUploadedFilePath($this->attribute);
		$path = $this->getFilePath();

		if($oldPath !== $path && !empty($oldPath) && !empty($path)){
			@mkdir(pathinfo($path, PATHINFO_DIRNAME), 0777, true);
			@rename($oldPath, $path);
		}
	}
}