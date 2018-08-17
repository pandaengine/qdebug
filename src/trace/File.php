<?php 
namespace qdebug\trace;
/**
 * 脚本运行包含的文件
 * 使用到的文件
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class File extends Trace{
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '文件';
	}
	/**
	 * @see \qdebug\trace\TraceInterface::log()
	 */
	public function log(){
		$files  =get_included_files();
		$fileTmp=[];
		foreach ($files as $key=>$file){
			$fileTmp[] = ''.$file.' ( '.number_format(filesize($file)/1024,2).' KB )';
		}
		return $fileTmp;
	}
	/**
	 * @see \qdebug\trace\TraceInterface::format()
	 */
	public function format(array $data){
		array_unshift($data,$this->color_red('共加载了 '.count($data).' 个文件'));
		return $data;
	}
}
?>