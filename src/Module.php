<?php
namespace qdebug;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Module extends \qing\app\Module{
	/**
	 * @var string
	 */
	public $namespace='qdebug';
	/**
	 * 模块目录
	 *
	 * @name modPath basePath
	 * @var string
	 */
	protected $basePath=__DIR__;
	/**
	 *
	 * @return $viewsPath
	 */
	public function getViewsPath(){
		return __DIR__.DS.DKEY_VIEWS;
	}
}
?>