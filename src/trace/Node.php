<?php 
namespace qdebug\trace;
use qing\filesystem\FileSize;
/**
 * 运行时节点时间和内存记录
 * 注意：重用代码多次调用的覆盖性能问题
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Node extends Trace{
	/**
	 * 节点栈
	 * 
	 * @var array
	 */
	protected $nodes=[];
	/**
	 * 
	 * @var array
	 */
	protected $nodeList=[];
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '节点';
	}
	/**
	 * 运行节点/运行时重要节点记录
	 * 添加节点,计算节点间运行时间和内存
	 * 
	 * ---
	 * 节点列表
	 * 1.应用开始
	 * 2.框架开始
	 * 3.路由开始-路由结束
	 * 4.控制器开始（Action）
	 * 5.是否有Model(每条SQL的执行时间)
	 * 6.Model结束（整个模型，所有sql操作耗的时间）
	 * 7.视图解析开始-视图完成
	 * 8.应用结束
	 * ---
	 * 
	 * @param string $name 节点名称
	 * @param string $summ 当前节点描述/有利于可读性
	 */
	public function addNode($name,$summ=''){
		$node=[];
		$node['time']  = microtime(true);
		$node['memory']= memory_get_usage();
		$node['summ']  = $summ;
		if(!isset($this->nodes[$name])){
			//#新增节点|开始节点
			$this->nodes[$name]['start']=$node;
			$this->nodeList[]=['node'=>$name,'status'=>'start'];
		}else{
			//#结束节点
			$this->nodes[$name]['end']=$node;
			$this->nodeList[]=['node'=>$name,'status'=>'end'];
		}
	}
	/**
	 * 获取节点
	 */
	public function log(){
		$base=Base::sgt();
		return 
		[
			'nodeList'	=>$this->nodeList,
			'nodes'		=>$this->nodes,
			'beginTime' =>$base->base['beginTime'],
			'beginMem'  =>$base->base['beginMem'],
		];
	}
	/**
	 * 
	 * @param array $data
	 */
	public function format(array $data){
		//dump($data);
		$nodes 			= $data['nodes'];
		$nodeList 		= $data['nodeList'];
		$_beginTime_    = $data['beginTime'];
		$_beginMemory_  = $data['beginMem'];
		
		$theme     ="{status}
					 <b style='color:#555;'>{node}</b>
					 <span>{summ}</span>
					 <span style='color:red;'>{cost}</span>
					 <span style='float:right;' title='当前内存: {memory}'>&nbsp; ↑ {memoryUp} </span>
					 <span style='float:right;' title='当前时间: {time}'> ↑ {timeUp} </span>
					";
		$content=array();
		$content[0]=" 起始时间: {$_beginTime_} &nbsp;&nbsp; 起始内存: ".FileSize::autoFormat($_beginMemory_);
		foreach ($nodeList as $v){
			$node     =$v['node'];
			$status   =$v['status'];
			$timeNow  =$nodes[$node][$status]['time'];
			$memoryNow=$nodes[$node][$status]['memory'];
			$summ	  =$nodes[$node][$status]['summ'];
			$row=array();
			$row['{node}']  = $node;
			$row['{status}']= ($status=='start')?"<span style='color:#03D203;'>[ start ]</span>":"<span style='color:red;'>[ end ]</span>";
			$row['{summ}']  = $summ>''?" ( {$summ} ) ":'';
			$row['{time}']  = $timeNow;
			$row['{memory}']= FileSize::autoFormat($memoryNow);
			$row['{timeUp}']  = number_format($timeNow-$_beginTime_,4)." s";
			$row['{memoryUp}']= FileSize::autoFormat($memoryNow-$_beginMemory_);
			if($status=='end'){
				//区间消耗
				$costTime  =number_format($timeNow-$nodes[$node]['start']['time'],4)." s";
				$memoryCost=$memoryNow-$nodes[$node]['start']['memory'];
				$memoryCost=FileSize::autoFormat($memoryCost);
				$row['{cost}']="区间消耗： {$costTime} / {$memoryCost}";
			}else{
				$row['{cost}']="";
			}
			//$content[]=str_replace(array_keys($row),array_values($row),$theme);
			$content[]=strtr($theme,$row);
		}
		return $content;
	}
}
?>