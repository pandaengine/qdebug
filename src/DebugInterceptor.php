<?php
namespace qdebug;
use qing\interceptor\Interceptor;
use qing\facades\Request;
//use qing\utils\Runtime;
use qing\filesystem\FileSize;
use qing\facades\Event;
use qing\db\Connection;
use qdebug\utils\Cache;
use qdebug\trace\Base;
use qdebug\trace\Node;
use qdebug\trace\Backtrace;
use qdebug\trace\SQL;
use qdebug\trace\ServerInfo;
use qdebug\trace\File;
use qdebug\trace\ClassFuncTraits;
use qdebug\trace\Console;
/**
 * trace追踪信息拦截器
 * 
 * - 注入各个事件点监听器
 * - 监控运行信息
 * 
 * # 记录规则
 * - 所有请求都记录，包括get/post/ajax
 * - 只有get显示前端logo
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class DebugInterceptor extends Interceptor{
	/**
	 * 开启
	 * 
	 * @var boolean
	 */
	public $on=true;
	/**
	 * 显示logo
	 *
	 * @var boolean
	 */
	public $showLogo=true;
	/**
	 * 是否在qdebug内部
	 *
	 * @var boolean
	 */
	protected $inQDebug=false;
	/**
	 */
	protected static $_instance;
	/**
	 * @return $this
	 */
	public static function instance(){
		if(!self::$_instance){
			throw new \Exception('qdebug拦截器未初始化');
		}
		return self::$_instance;
	}
	/**
	 * 前置拦截
	 * 
	 * @see \qing\interceptor\Interceptor::preHandle()
	 */
	public function preHandle(){
		if(!$this->on || APP_DEBUG!==true){
			$this->on=false;
			return true;
		}
		$this->on=true;
		self::$_instance=$this;
		require_once __DIR__.'/common/function.php';
		//是否挂载了qdebug组件
		define('QDEBUG',true);
		qdebug_node('app_life','应用生命周期/开始');
		qdebug_console('QINGMVC.PHP CONSOLE INFO');
		qdebug_console(__METHOD__);
		//记录执行时间点和内存点
		//qdebug_base('beginTime',microtime(true));
		qdebug_base('beginTime',$_SERVER['REQUEST_TIME_FLOAT']);
		qdebug_base('beginMem' ,memory_get_usage());
		//
		$pathinfo=(string)$_SERVER['PATH_INFO'];
		$pathinfo=ltrim($pathinfo,'/');
		$this->inQDebug=preg_match('/^\.qdebug/i',$pathinfo)>0;
		if($this->inQDebug){
			$this->initQDebug();
		}
		//$this->registerEvent();
		$this->registerSqlEvent();
		return true;
	}
	/**
	 * 初始化qdebug模块
	 */
	protected function initQDebug(){
		//#新增模块
		$module=
		[
			'class'		=>'\qdebug\Module',
			'classFile'	=>__DIR__.'/Module.php',
		];
		app()->setModule('qdebug',$module);
		coms()->set('view.qdebug',['class'=>'\qing\view\View']);
	}
	/**
	 * @see \qing\interceptor\Interceptor::afterCompletion()
	 */
	public function afterCompletion(){
		if($this->inQDebug){
			return;
		}
		if(!$this->on){
			return;
		}
		//
		$endTime=microtime(true);
		$endMem =memory_get_usage();
		$runtime=number_format($endTime-qdebug_base('beginTime'),4);
		$runmem =$endMem-qdebug_base('beginMem');
		$runmem =FileSize::autoFormat($runmem);
		qdebug_base('endTime',$endTime);
		qdebug_base('endMem',$endMem);
		qdebug_base('runtime',$runtime);
		qdebug_base('runmem',$runmem);
		//post请求不显示，但仍然记录信息
		if($this->showLogo && Request::isGet()){
			include __DIR__.'/views/logo.html';
		}
		//
		$this->cacheEvents();
		qdebug_console(__METHOD__);
		qdebug_node('app_life','应用生命周期/结束');
		$this->logTrace();
		return true;
	}
	/**
	 * 
	 */
	protected function logTrace(){
		//dump(__METHOD__);
		$traces=[];
		$traces[]=Base::sgt();
		$traces[]=File::sgt();
		$traces[]=ServerInfo::sgt();
		$traces[]=ClassFuncTraits::sgt();
		$traces[]=Backtrace::sgt();
		$traces[]=Node::sgt();
		$traces[]=SQL::sgt();
		$traces[]=Console::sgt();
		$datas=[];
		foreach($traces as $trace){
			$datas[get_class($trace)]=$trace->log();
		}
		Cache::set($datas);
	}
	/**
	 * 缓存所有的事件点信息
	 *
	 * @return boolean
	 */
	protected function cacheEvents(){
		return;
		$hooks=Event::getHooks();
		dump($hooks);
		dump(Event::getInstance());
	}
	/**
	 * 注册事件监听处理器
	 * 
	 * @return boolean
	 */
	protected function registerEvent(){
		return true;
	}
	/**
	 * 注册Sql查询监听处理器
	 *
	 * @return boolean
	 */
	protected function registerSqlEvent(){
		$sql=SQL::sgt();
		Event::bind(Connection::E_QUERY_BEFORE,function(Connection $conn)use($sql){
			$sql->add($conn);
		});
	}
}
?>