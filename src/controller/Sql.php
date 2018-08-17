<?php
namespace qdebug\controller;
use qing\utils\Runtime;
use qing\filesystem\FileSize;
use qing\mv\MV;
use qing\db\Db;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Sql extends Base{
	/**
	 * @return string
	 */
	protected function connName(){
		if(isset($_GET['conn'])){
			$conn=$_GET['conn'];
		}else{
			$conn=(string)$_POST['conn'];
		}
		return $conn;
	}
	/**
	 * 浏览器主连接
	 * 
	 * @return \qing\db\pdo\Connection
	 */
	protected function getConn(){
		$conn=$this->connName();
		if(Db::has($conn)){
			//指定连接
			$conn=Db::conn($conn);
		}else{
			//默认主连接
			$conn=Db::conn();
		}
		return $conn;
	}
	/**
	 * 默认操作首页
	 */
	public function index(){
		$this->setTitle('SQL调试');
		$sql   ='';
		$result=[];
		if($_POST){
			$sql=$_POST['sql'];
			if($sql){
				$sql   =trim($sql);
				$result=$this->executeSql($sql);
			}
		}else{
			$sql=$_GET['sql'];
		}
		$vars=[];
		$vars['sql']   =$sql;
		$vars['result']=$result;
		$vars['error'] =$this->error;
		$vars['conn']  =$this->connName();
		return $this->render('',$vars);
	}
	/**
	 *
	 * @param string $sql
	 * @param array  $params
	 * @return array
	 */
	protected function executeSql($sql){
		$cat=__METHOD__;
		$res=[];
		try{
			//#计算执行时间/内存
			Runtime::begin($cat);
			Runtime::mem_begin($cat);
			
			$result=$this->getConn()->query($sql);
			
			$time=Runtime::end($cat);
			$mem=Runtime::mem_end($cat);
			
			$res['result'] =$result;
			$res['rownum'] =count($result);
			$res['runtime']=$time;
			$res['runmem'] =FileSize::autoFormat($mem);
			
			//$this->excuteLog($res,$sql,$params);
		}catch(\Exception $e){
			//dump($e);
			$this->error=$e->getMessage();
		}
		return $res;
	}
	/**
	 * 进程
	 */
	protected static function getProcessesTitle($k){
		$title=[
			'Id'	=>'ID',
			'User'	=>'用户',
			'Host'	=>'主机',
			'db'	=>'数据库',
			'Command'=>'命令',
			'State'  =>'状态',	
		];
		return isset($title[$k])?$title[$k]:$k;
	}
	/**
	 * 进程
	 * select * from information_schema.`PROCESSLIST`
	 * SHOW FULL PROCESSLIST
	 * 
	 * @see \qing\db\ddl\Thread
	 */
	public function processes(){
		$row=(array)$this->getConn()->query('SHOW FULL PROCESSLIST');
		$row=$row[0];
		$list=[];
		$list['进程']="<a href='".U('','kill',['id'=>$row['Id']])."'>杀死</a>";
		foreach($row as $k=>$v){
			$k=self::getProcessesTitle($k);
			$list[$k]=$v;
		}
		$vars=[];
		$vars['list']=[$list];
		return $this->render('',$vars);
	}
	/**
	 * 杀死进程
	 *
	 * @see \qing\db\ddl\Thread
	 */
	public function kill(){
		$id=(int)$_GET['id'];
		$conn=$this->getConn();
		$res=$conn->execute('kill '.$id);
		if(!$res){
			return MV::error('杀死失败：'.$conn->getConnError());
		}else{
			return MV::success();
		}
	}
	/**
	 * 慢查询
	 * 
	 * 不要分号|一个分号一个语句|有分号两个语句?
	 */
	public function slow(){
		$conn=$this->getConn();
		$tests=[];
		
		//#变量
		$sql='show variables like "%slow%" ';
		$tests[$sql]=$conn->query($sql);
		
		$sql='show variables like "%long%" ';
		$tests[$sql]=$conn->query($sql);
		
		$sql='show variables like "log_output" ';
		$tests[$sql]=$conn->query($sql);
		
		//#状态
		$sql='show global status like "%slow%" ';
		$tests[$sql]=$conn->query($sql);
		
		$sql='show status like "%slow%" ';
		$tests[$sql]=$conn->query($sql);
		
		$sql='show variables like "slow_query_log_file"';
		$slow_query_log_file=$conn->query($sql);
		$slow_query_log_file=(string)current($slow_query_log_file)['Value'];
		
		$vars=[];
		$vars['slow_query_log_file']=$slow_query_log_file;
		$vars['tests']=$tests;
		return $this->render('',$vars);
	}
	/**
	 * 
	 */
	public function slowlog(){
		$logfile=(string)@$_GET['file'];
		if(!is_file($logfile)){
			return MV::error('慢查询日志文件不存在');
		}
		$text=file_get_contents($logfile);
		$vars=[];
		$vars['log']=$text;
		$vars['logfile']=$logfile;
		return $this->render('',$vars);
	}
}
?>