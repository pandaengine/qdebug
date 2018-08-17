<?php 
namespace qdebug\trace;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Console extends Trace{
	/**
	 * @var array
	 */
	protected $info=[];
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '控制台';
	}
	/**
	 * @see \qdebug\trace\TraceInterface::log()
	 */
	public function log(){
		return $this->info;
	}
	/**
	 * @see \qdebug\trace\TraceInterface::format()
	 */
	public function format(array $data){
		return $data;
	}
	/**
	 * 添加控制台信息
	 * 
	 * @param $value
	 */
	public function addConsole($value){
		$this->info[]=$value;
	}
}
?>