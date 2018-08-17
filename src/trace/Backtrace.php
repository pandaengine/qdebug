<?php 
namespace qdebug\trace;
use qing\utils\ObjectDump;
/**
 * 运行回溯信息
 * - 一般跟踪是到控制器显示视图时的信息
 * - 不同的位置初始化产生的追溯数据不一样
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Backtrace extends Trace{
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '回溯&断点';
	}
	/**
	 * 回溯内容
	 * 
	 * @var string
	 */
	protected $traces=[];
	/**
	 * DEBUG_BACKTRACE_PROVIDE_OBJECT	是否填充 "object" 的索引。
	 * 
	 * @return string
	 */
	public function log(){
		$traces=debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$this->addBacktrace($traces,'');
		return $this->traces;
	}
	/**
	 * @param array $traces
	 * @param string $name
	 */
	public function addBacktrace(array $traces,$name=''){
		$this->traces[]=[$traces,$name];
	}
	/**
	 * 
	 * @param string $filename
	 * @return string
	 */
	protected function getSubFile($filename){
		$match=preg_match('/^(.*)\\\\.{50,}/i',$filename,$matchs);
		if(!(bool)$match){
			return $filename;
		}
		$filename=substr($filename,strlen($matchs[1]));
		return '...'.$filename;
		//前瞻断言,正面断言： \w+(?=;) 匹配一个单词紧跟着一个分号但是匹配结果不会包含分号，
		//$filename=preg_replace('/^(.*)(?=\\\\.{50,})/i','',$filename);
		//return '...'.$filename;
		//return substr($filename,-50);
	}
	/**
	* krsort() 函数将数组按照键逆向排序，为数组值保留原来的键。
	* 
	* @return array
	*/
	public function format(array $data){
		//格式化
		$list=[];
		foreach($data as $k=>$trace){
			$name=$k;
			if($trace[1]){
				$name=$trace[1];
			}
			$list[$name.' [ 0 ]']=['file'=>$this->color_red("断点: {$name}")];
			krsort($trace[0]);
			$i=1;
			foreach($trace[0] as $key=>$row){
				$list[$name." [ {$i} ]"]=$row;
				$i++;
			}
		}
		$news=[];
		foreach($list as $k=>$t){
			$news[$k]=$this->format_row($t);
		}
		return $news;
	}
	/**
	 * @param array $t
	 * @return string
	 */
	protected function format_row(array $t){
		static $traceTheme="<span class='backtrace-item'> {file} {line} 
						  <span class='method'>
						  	<span class='class'>{class}</span>
						  	<span class='type'>{type}</span>
							<span class='func'>{function}</span>
							<span class='args'>{args}</span>
						  </span>
						  </span>";
	    $row=[];
	    if(isset($t['file'])){
			$row['{file}']  = $this->getSubFile($t['file']);
			$row['{file}']  = '<span title="'.$t['file'].'">'.$row['{file}'].'</span>';
		}else{
	     	$row['{file}']	= '无法定位文件';
		}
		if(isset($t['line'])){
			$row['{line}']  = '( '.(int)@$t['line'].' )';
		}else{
			$row['{line}']  = '';
		}
		$row['{class}'] 	= @$t['class'];
		$row['{type}']  	= @$t['type'];
		$row['{function}']	= $t['function'];
		if(isset($t['args'])){
			$args=(array)$t['args'];
			$args=implode(' , ',array_map([$this,'dumpVar'],$args) );
			$row['{args}']  = "( {$args} )";
		}else{
			$row['{args}']  = '';
		}
     	//return str_replace(array_keys($row),array_values($row), $traceTheme);
     	return strtr($traceTheme,$row);
	}
	/**
	 * 
	 * @param mixed $var
	 */
	protected function dumpVar($var){
		if(is_string($var) && preg_match('/^Object\(/i',$var)>0){
			return $var;
		}
		return ObjectDump::toString($var);
	}
}
?>