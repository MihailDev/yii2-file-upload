<?php
/**
 * Date: 08.08.14
 * Time: 17:47
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

/**
 * Interface HandlerInterface
 *
 * @package mihaildev\fileupload\handler
 */
interface HandlerInterface {
	/**
	 * Before validate event.
	 *
	 * @param $oldModel FileUploadBehavior
	 */
	public function beforeValidate($oldModel);

	/**
	 * Before save event.
	 *
	 * @param $oldModel FileUploadBehavior
	 */
	public function beforeSave($oldModel);

	/**
	 * After save event.
	 *
	 * @param $oldModel FileUploadBehavior
	 */
	public function afterSave($oldModel);

	/**
	 * Delete files.
	 */
	public function delete();

	/**
	 * Return file url.
	 *
	 * return string
	 */
	public function getFileUrl();

	/**
	 * Return file path.
	 *
	 * return string
	 */
	public function getFilePath();
} 