<?php 
namespace qdebug\trace;
use qing\db\Connection;
use qing\utils\Runtime;
use qing\filesystem\FileSize;
use qdebug\utils\Table;
use qing\db\Db;
/**
 * sql请求执行数据
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class SQL extends Trace{
	/**
	 * sql
	 * @var array
	 */
	protected $sql=[];
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return 'SQL';
	}
	/**
	 * @return array
	 */
	public function log(){
		return $this->sql;
	}
	/**
	 * 添加一条记录
	 *
	 * @param Connection $conn
	 */
	public function add(Connection $conn){
		$sql   =$conn->getSql();
		$params=$conn->getLastParams();
		$query ='';
		if($params){
			$query =$conn->getLastSql();
		}
		$this->sql[]=['sql'=>$sql,'query'=>$query,'params'=>$params,'conn'=>Db::connName($conn->comName)];
	}
	/**
	 * @return array
	 */
	public function format(array $list){
// 		dump($list);
		//所有sql语句
		$allSql='';
		foreach($list as $k=>$row){
			if($allSql>''){
				$allSql.=";\n";
			}
			$allSql.=$row['sql'];
		}
		$count=count($list);
		$html=[];
		$html[]='<a target="_blank" href="'.U('sqlmulti','',['conn'=>$row['conn'],'sql'=>$allSql]).'" style="color:red;"><b>[ 调试所有SQL ] 共'.$count.'条</b></a>';
		foreach($list as $k=>$row){
			$row=$this->getCost($row);
			$html[]=$this->format_row($row);
		}
		return $html;
	}
	/**
	 * @param array $row
	 */
	protected function format_row(array $row){
		//dump($row);exit();
		$sql=(string)$row['sql'];
		if(strpos($sql,"\r")!==false){
			//#多行的时候
			$sql="<pre>{$sql}</pre>";
		}
		$vars=[];
		$vars['{sql}']    =$sql;
		$vars['{result}'] =$row['result'];
		$vars['{results}']=$row['results'];
		$prequery='';
		if($row['params'] && $row['query']){
			$prequery.="<br/>预处理语句: ".$row['query'];
			$prequery.="<br/>预处理参数: ".json_encode($row['params']);
		}
		$vars['{prequery}']=$prequery;
		$vars['{cost}']="<span style='color:green;'> 执行消耗： {$row['runtime']} S / {$row['runmem']} </span>";
		
		$id=uniqid();
		$id='J-result-'.$id;
		$sqlBug=U('sql','',['conn'=>$row['conn'],'sql'=>$sql]);
		$theme="
				<span class='query'>{sql}</span>
				&nbsp;
				<a target='_blank' href='{$sqlBug}' style='color:red;'>调试</a>
				{cost}
				<a class='explain-plus' href='javascript:void(0);' onclick='$(\"#{$id}\").toggle();'>[ 查看结果 ({results}) ]</a>
				{prequery}
				<div class='result-box' style='display:none;' id='{$id}'>
				{result}
				</div>
				";
		//return str_replace(array_keys($vars),array_values($vars),$theme);
		return strtr($theme,$vars);
	}
	/**
	 * 计算sql执行时间和内存
	 * 
	 * @param array $row
	 */
	protected function getCost(array $row){
		//#计算执行时间/内存
		$cat=__METHOD__;
		Runtime::begin($cat);
		Runtime::mem_begin($cat);
		
		$method='query';
		$row['is_execute']=false;
		if(preg_match('/^(insert|replace|delete|update)/i',$row['sql'])>0){
			$method='execute';
			$row['is_execute']=true;
		}
		/*@var $com \qing\db\Connection */
		$conn=Db::conn($row['conn']);
		if($row['params'] && $row['query']){
			//使用预处理
			$result=$conn->$method($row['query'],(array)$row['params']);
		}else{
			//sql语句
			$result=$conn->$method($row['sql']);
		}
		$time=Runtime::end($cat);
		$mem=Runtime::mem_end($cat);
		//$row['result']  =$result;
		$row['runtime'] =$time;
		$row['runmem']  =FileSize::autoFormat($mem);
		if(is_array($result)){
			$row['result'] =Table::show($result);
			$row['results']=count($result).'条记录';
		}elseif(is_bool($result)){
			$row['result'] =$result?'true':'false';
			$row['results']=$row['result'];
		}else{
			$row['result'] =(string)$result;
			$row['results']=$row['result'];
		}
		return $row;
	}
}
?>