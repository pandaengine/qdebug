<?php 
namespace qdebug\trace;
use qing\facades\Request;
//use qing\utils\Time;
//use qing\filesystem\FileSize;
/**
 * 应用基础信息
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Base extends Trace{
	/**
	 * @var array
	 */
	public $base=[];
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '基本';
	}
	/**
	 * @see \qdebug\trace\TraceInterface::log()
	 */
	public function log(){
		//#信息
		$base=$this->base;
		$base['session_id']  	=session_id();
		$base['session_name']  	=session_name();
		$base['url']	 		=Request::getRequestUri();
		$base['request'] 		=$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' '.date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
		return $base;
	}
	/**
	 * @see \qdebug\trace\TraceInterface::format()
	 */
	public function format(array $row){
		/*
		$utime=function($utime){
			return date('H:i:s',(int)$utime).' '.Time::usec($utime).' s ';
		};
		*/
		$base=
		[
			'请求地址 '  =>  $this->color_red($row['url']),
			'请求信息'  =>  $row['request'],
			'运行时间'  =>  $this->color_red($row['runtime'].'s'),
			'运行内存'  =>  $this->color_red($row['runmem']),
			//'运行周期'  =>  $utime($row['beginTime']).' ~ '.$utime($row['endTime']),
			'会话信息'  =>  $row['session_name'].'='.$row['session_id'],
		];
		//隐藏的原始数据
		$base['_data']=$row;
		return $base;
	}
	/**
	 * @param string $key
	 * @param string $value
	 */
	public function setBase($key,$value){
		$this->base[$key]=$value;
	}
}
?>