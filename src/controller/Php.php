<?php
namespace qdebug\controller;
use qing\utils\Runtime;
use qing\utils\Output;
use qing\filesystem\FileSize;
/**
 *
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Php extends Base{
	/**
	 * @var string
	 */
	protected $error='';
	/**
	 * 默认操作首页
	 */
	public function index(){
		$code   ='';
		$result=[];
		if($_POST){
			$code=$_POST['code'];
			$code=trim($code);
			if($code){
				$result=$this->execute($code);
			}
		}
		$vars=[];
		$vars['code']   =$code;
		$vars['result']=$result;
		$vars['error'] =$this->error;
		return $this->render('',$vars);
	}
	/**
	 *
	 * @param string $code
	 * @return array
	 */
	protected function execute($code){
		$cat=__METHOD__;
		$res=[];
		try{
			//#计算执行时间/内存
			Runtime::begin($cat);
			Runtime::mem_begin($cat);
			
			Output::capture_begin();
			
			//#执行php代码
			//危险，不要在线上环境安装qdebug模块
			//@eval($code);
			eval($code);
			
			$result=Output::capture_end();
			
			$time=Runtime::end($cat);
			$mem=Runtime::mem_end($cat);
			
			$res['result'] =$result;
			$res['runtime']=$time;
			$res['runmem'] =FileSize::autoFormat($mem);
			
		}catch(\Exception $e){
			//dump($e);
			$this->error=$e->getMessage();
			return false;
		}
		return $res;
	}
}
?>