<?php
namespace qdebug\controller;
use qing\utils\Runtime;
use qing\filesystem\FileSize;
/**
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Sqlmulti extends Sql{
	/**
	 * 默认操作首页
	 */
	public function index(){
		$this->setTitle('多条SQL调试');
		$sql='';
		$result=[];
		if($_POST){
			$sql=$_POST['sql'];
			$explain=(int)$_POST['explain'];
			if($sql){
				$sql   =trim($sql);
				$result=$this->executeSqls($sql,$explain);
				//dump($result);
				//exit();
			}
		}else{
			$sql =(string)$_GET['sql'];
		}
		
		$vars=[];
		$vars['sql']	=$sql;
		$vars['result']	=$result;
		$vars['error']  =$this->error;
		$vars['conn']   =$this->connName();
		return $this->render('',$vars);
	}
	/**
	 * @param string $sqls
	 * @param string $explain
	 * @return array
	 */
	protected function executeSqls($sqls,$explain){
		$sqls=(array)explode(';',$sqls);
		$news=[];
		//#解释
		if($explain){
			foreach($sqls as $sql){
				if(!$sql){
					continue;
				}
				$expsql='explain '.$sql;
				$news[]=$expsql;
				$news[]=$sql;
			}
		}else{
			$news=$sqls;
		}
		//#计算执行时间/内存
		$cat=__METHOD__;
		Runtime::begin($cat);
		Runtime::mem_begin($cat);
		
		$rows=[];
		foreach($news as $sql){
			if(!$sql){
				continue;
			}
			$row=[];
			$row['sql']=$sql;
			
			$res=$this->executeSql($sql);
			if(!$res){
				//#执行失败
				$row['success']=false;
				$row['error'] =$this->error;
			}else{
				$row['success']=true;
				$row['result'] =$res;
			}
			$rows[]=$row;
		}
		
		$time=Runtime::end($cat);
		$mem=Runtime::mem_end($cat);
		$all=[];
		$all['rows']   =$rows;
		$all['runtime']=$time;
		$all['runmem'] =FileSize::autoFormat($mem);
		return $all;
	}
}
?>