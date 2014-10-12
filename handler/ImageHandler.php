<?php
/**
 * Date: 08.08.14
 * Time: 2:10
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
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * Class ImageHandler
 *
 * @package mihaildev\fileupload\handler
 */
class ImageHandler extends FileHandler{

	public function init(){
		if(!isset($this->options['thumbs']))
			$this->options['thumbs'] = [];
	}

	/**
	 * @param $oldModel FileUploadBehavior
	 */
	public function afterSave($oldModel){
		parent::afterSave($oldModel);

		if ($this->_file instanceof UploadedFile) {
			foreach($this->options['thumbs'] as $id=>$options){
				if(isset($options['imagine']) || $options['saveOptions']){
					if(empty($options['saveOptions']))
						$options['saveOptions'] = [];

					if(empty($options['imagine']))
						$options['imagine'] = function($filename){
							return Image::getImagine()->open($filename);
						};


					$this->processImage($options['imagine'], $options['saveOptions'], $id);
				}
			}

			if(isset($this->options['imagine']) || $this->options['saveOptions']){
					if(empty($this->options['saveOptions']))
						$this->options['saveOptions'] = [];

					if(empty($this->options['imagine']))
						$this->options['imagine'] = function($filename){
							return Image::getImagine()->open($filename);
						};

				$this->processImage($this->options['imagine'], $this->options['saveOptions']);
			}
		}
	}

	protected function processImage($imagine, $saveOptions = [], $id = ''){

		$image = call_user_func($imagine, $this->getFilePath());

		$imagePath = $this->getFilePath($id);
		@mkdir(pathinfo($imagePath, PATHINFO_DIRNAME), 0777, true);

		$image->save($imagePath, $saveOptions);
	}

	public function getFilePath($id = '')
	{
		if(!empty($this->_filePath['_'.$id]))
			return $this->_filePath['_'.$id];

		if(empty($this->behavior->owner->{$this->attribute}))
			return null;
		if(empty($id))
			$this->_filePath['_'.$id] =  $this->getPath($this->options['path'], $this->attribute, $this->behavior->owner->{$this->attribute});
		else
			$this->_filePath['_'.$id] =  $this->getPath($this->options['thumbs'][$id]['path'], $this->attribute, $this->behavior->owner->{$this->attribute}, $id);

		return $this->_filePath['_'.$id];
	}

	public function getFileUrl($id = '')
	{
		if(!empty($this->_fileUrl['_'.$id]))
			return $this->_fileUrl['_'.$id];

		if(empty($this->behavior->owner->{$this->attribute}))
			return "";

		if(empty($id))
			$this->_fileUrl['_'.$id] =  $this->getPath($this->options['url'], $this->attribute, $this->behavior->owner->{$this->attribute});
		else
			$this->_fileUrl['_'.$id] =  $this->getPath($this->options['thumbs'][$id]['url'], $this->attribute, $this->behavior->owner->{$this->attribute}, $id);

		return $this->_fileUrl['_'.$id];
	}

	/**
	 * @param $oldModel FileUploadBehavior
	 * @param string $id
	 */
	protected  function _renew($oldModel, $id = ''){
		$oldPath = $oldModel->getUploadedFilePath($this->attribute, $id);
		$path = $this->getFilePath($id);

		if($oldPath !== $path && !empty($oldPath) && !empty($path)){
			@mkdir(pathinfo($path, PATHINFO_DIRNAME), 0777, true);
			@rename($oldPath, $path);
		}
	}

	public function renew($oldModel){
		if(empty($oldModel))
			return;
		$this->_renew($oldModel);

		foreach($this->options['thumbs'] as $id=>$options){
			$this->_renew($oldModel, $id);
		}
	}

	/**
	 * @param $model FileUploadBehavior
	 */
	protected function deleteFiles($model){
		$this->_deletePaths[] = $model->getUploadedFilePath($this->attribute);

		foreach($this->options['thumbs'] as $id=>$options){
			$this->_deletePaths[] = $model->getUploadedFilePath($this->attribute, $id);
		}
	}
} 