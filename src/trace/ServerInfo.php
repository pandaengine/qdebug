<?php 
namespace qdebug\trace;
/**
 * 服务器信息和用户数据
 *  
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class ServerInfo extends Trace{
	/**
	 * @see \qdebug\trace\TraceInterface::title()
	 */
	public function title(){
		return '服务器信息';
	}
	/**
	 * @see \qdebug\trace\TraceInterface::log()
	 */
	public function log(){
		$datas=
		[
				"[ GET ]"       => (array)$_GET,
				"[ POST ]"      => (array)$_POST,
				"[ FILES ]"     => (array)$_FILES,
				"[ COOKIE ]"    => (array)$_COOKIE,
				"[ SESSION ]"   => (array)$_SESSION,
				"[ SERVER  ]"   => (array)$_SERVER,
				//"[ GLOBALS ]"   => (array)[],
				];
		return $datas;
	}
	/**
	 * @see \qdebug\trace\TraceInterface::format()
	 */
	public function format(array $list){
		$rows=[];
		$rows['+/-']="<a href='javascript:void(0);'
						 onclick='$(this).parents(\"ul\").find(\".table-box\").toggle();' 
						 title='切换：折叠/展开'>[折叠全部]</a>";
		foreach($list as $k=>$row){
			$table=$this->getTable($row);
			$k="<b>{$k}</b>";
			$rows[$k]=$this->format_row($table);
		}
		return $rows;
	}
	/**
	 * @param array $row
	 */
	protected function format_row($table){
		$id=uniqid();
		$id='J-table-'.$id;
		$theme="<a href='javascript:void(0);' onclick='$(\"#{$id}\").toggle();' onclick2='table_toggle(\"#{$id}\");' title='切换：折叠/展开'>[折叠]</a>
				<div class='table-box' style='display:;padding:10px;' id='{$id}'>
				{$table}
				</div>
				";
		return $theme;
	}
	/**
	 * get sql explain table
	 * 
	 * @param array $datas
	 * @return string
	 */
	protected function getTable(array $datas,$title=''){
		$tb='';
		if(!$datas){
			$tb.='空';
		}else{
			$tb.="<table class='tb-col2'>";
			foreach($datas as $k=>$v){
				$tb.="<tr>";
				$tb.="<td>{$k}</td>";
				$tb.="<td>{$v}</td>";
				$tb.="</tr>";
			}
			$tb.="</table>";
		}
		return $tb;
	}
}
?>