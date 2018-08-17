<?php
namespace qdebug\controller;
use qdebug\utils\Cache;
use qing\utils\ClassName;
use qing\filesystem\RM;
use qing\mv\MV;
/**
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Index extends Base{
	/**
	 * 默认操作首页
	 */
	public function index(){
		$sessions=Cache::get_sessions();
		$id=$_GET['sessid'];
		if(!$id && $sessions){
			//取最新会话
			$id=$sessions[count($sessions)-1][0];
		}
		$titles=[];
		$list  =[];
		$traces=Cache::get($id);
		//格式化数据
		foreach($traces as $class=>$data){
			if(class_exists($class)){
				/*@var $formater \qdebug\trace\TraceInterface */
				$formater=new $class();
				$class	 =strtolower(ClassName::onlyName($class));
				$titles[$class]=$formater->title();
				$list[$class]  =$formater->format($data);
			}
		}
		$vars=[];
		$vars['sessid']	  =$id;
		$vars['list']	  =$list;
		$vars['base']	  =$list['base']['_data'];
		$vars['titles']	  =$titles;
		$vars['sessions'] =$sessions;
		return $this->render('',$vars);
	}
	/**
	 *
	 */
	public function clearSess(){
		$path=Cache::cachePath();
		$res =true;
		if(is_dir($path)){
			$res=RM::dir($path);
		}
		if($res){
			return MV::success('清除成功');
		}else{
			return MV::success('清除失败');
		}
	}
}
?>