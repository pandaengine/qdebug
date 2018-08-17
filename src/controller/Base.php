<?php
namespace qdebug\controller;
use qing\controller\Controller;
/**
 * 控制器基类
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Base extends Controller{
	/**
	 * @var string
	 */
	protected $error='';
	/**
	 * 返回mv由前端控制器渲染
	 *
	 * @param string $viewName  模版文件
	 * @param array  $vars      模版变量
	 * @return \qing\mvc\ModelAndView
	 */
	protected function render($viewName='',array $vars=[]){
		$mv=new \qing\mv\ModelAndView($viewName,$vars);
		$mv->render='view.qdebug';
		return $mv;
	}
	/**
	 * - 避免和主应用冲突
	 * - 需要前缀qdebug
	 * 
	 * @param string $title
	 * @return boolean
	 */
	protected function setTitle($title){
		$GLOBALS['qdebug_title']=$title;
	}
}
?>