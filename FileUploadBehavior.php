<?php
/**
 * Date: 06.08.14
 * Time: 23:43
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\fileupload;
use mihaildev\fileupload\handler\FileHandler;
use yii\db\ActiveRecord;

/**
 * Class FileUploadBehavior
 *
 * @package mihaildev\fileupload
 */
class FileUploadBehavior extends \yii\base\Behavior{

	const DELETE_VALUE = 'DELETE_FILE';

	const HANDLER_FILE = 'mihaildev\fileupload\handler\FileHandler';
	const HANDLER_IMAGE = 'mihaildev\fileupload\handler\ImageHandler';

	public $attributes = [
		'file' => [
			'path' => '@webroot/files/<fileName>.<fileExtension>',
			'url' => '@web/files/<fileName>.<fileExtension>'
		]
	];

	public $replacePairs = [
		'<modelId>' => 'id'
	];

	public function init(){

		$attributes = $this->attributes;

		foreach($attributes as $attribute => $options){
			if(isset($options['handler'])){
				$handler = $options['handler'];
				unset($options['handler']);
			}else{
				$handler = FileHandler::className();
			}

			$this->attributes[$attribute] = new $handler([
				'attribute' => $attribute,
				'behavior' => $this,
				'options' => $options
			]);
		}
	}

	public function events()
	{
		return [
			ActiveRecord::EVENT_AFTER_FIND => 'fileUploadAfterFind',
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'fileUploadBeforeValidate',
			ActiveRecord::EVENT_BEFORE_INSERT => 'fileUploadBeforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'fileUploadBeforeSave',
			ActiveRecord::EVENT_AFTER_INSERT => 'fileUploadAfterSave',
			ActiveRecord::EVENT_AFTER_UPDATE => 'fileUploadAfterSave',
			ActiveRecord::EVENT_BEFORE_DELETE => 'fileUploadBeforeDelete',
		];
	}

	private $_oldModel;

	/**
	 * After find event.
	 */
	public function fileUploadAfterFind(){
		if(!$this->owner->isNewRecord){
			$this->_oldModel = clone $this->owner;
		}
	}

	/**
	 * Before validate event.
	 */
	public function fileUploadBeforeValidate()
	{
		foreach($this->attributes as $handler){
			/** @var $handler FileHandler */
			$handler->beforeValidate($this->_oldModel);
		}
	}

	/**
	 * Before save event.
	 */
	public function fileUploadBeforeSave()
	{
		foreach($this->attributes as $handler){
			/** @var $handler FileHandler */
			$handler->beforeSave($this->_oldModel);
		}
	}

	/**
	 * After save event.
	 */
	public function fileUploadAfterSave()
	{
		foreach($this->attributes as $handler){
			/** @var $handler FileHandler */
			$handler->afterSave($this->_oldModel);
		}
	}

	public function fileUploadBeforeDelete(){
		foreach($this->attributes as $handler){
			/** @var $handler FileHandler */
			$handler->delete();
		}
	}

	public function getUploadedFilePath($attributeName)
	{
		$args = func_get_args();
		array_shift($args);

		return call_user_func_array([$this->attributes[$attributeName], 'getFilePath'], $args);
	}

	public function getUploadedFileUrl($attributeName)
	{
		$args = func_get_args();
		array_shift($args);

		return call_user_func_array([$this->attributes[$attributeName], 'getFileUrl'], $args);
	}

	public function deleteFileUpload($attributeName){
		$this->attributes[$attributeName]->delete();
	}
} 