<?php
use qdebug\DebugInterceptor;
use qdebug\trace\Base;
use qdebug\trace\Node;
use qdebug\trace\Console;
use qdebug\trace\Backtrace;
/**
 * @return \qdebug\DebugInterceptor
 */
function qdebug(){
	return DebugInterceptor::instance();
}
/**
 * 添加控制台信息
 *
 * @param string $info 信息
 */
function qdebug_console($info){
	Console::sgt()->addConsole($info);
}
/**
 * 添加节点信息,计算节点间运行时间和内存
 *
 * @param string $name 节点名称
 * @param string $summ 节点描述
 */
function qdebug_node($name,$summ=''){
	Node::sgt()->addNode($name,$summ);
}
/**
 * 添加断点
 * 
 * @param string $name 断点名称
 */
function qdebug_breakpoint($name=''){
	//$traces=debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS);
	$traces=debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT);
	Backtrace::sgt()->addBacktrace($traces,$name);
}
/**
 * @param string $key
 * @param string $value
 */
function qdebug_base($key,$value=null){
	if($value===null){
		return Base::sgt()->base[$key];
	}else{
		Base::sgt()->setBase($key,$value);
	}
}
?>