<?php
/**
 * Date: 08.08.14
 * Time: 17:56
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
use yii\base\Object;

/**
 * Class BaseHandler
 *
 * @package mihaildev\fileupload\handler
 */
abstract class BaseHandler extends Object implements HandlerInterface{
	/**
	 * @var string
	 */
	public $attribute;

	/**
	 * @var FileUploadBehavior
	 */
	public $behavior;

	/**
	 * @var array
	 */
	public $options = [];
} 