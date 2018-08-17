<?php 
namespace qdebug\trace;
/**
 * 定义的类、函数、常量、接口、trait信息
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class ClassFuncTraits extends ServerInfo{
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '类&函数';
	}
	/**
	 * @see \qdebug\trace\TraceInterface::log()
	 */
	public function log(){
		$arr=[];
		$arr['[ 常量/constant ]']		=get_defined_constants(true)['user'];
		$arr['[ 类/class ]']			=get_declared_classes();
		$arr['[ 接口/interface ]']	=get_declared_interfaces();
		$arr['[ 函数/function ]']		=get_defined_functions()['user'];
		$arr['[ trait ]']			=get_declared_traits();
		return $arr;
	}
}
?>