<?php
namespace qdebug\utils;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Table{
	/**
	 * @param array $datas
	 * @return string
	 */
	public static function show(array $datas){
		$html='';
		$html.="<table class='table table-hover table-bordered'>";
		//#表格标题
		$html.="<tr>";
		//#取第一行
		foreach (array_keys((array)current($datas)) as $v){
			$html.="<th>$v</th>";
		}
		$html.="</tr>";
		foreach ($datas as $k=>$row){
			$html.="<tr>";
			foreach (array_values($row) as $v){
				$html.="<td>$v</td>";
			}
			$html.="</tr>";
		}
		$html.="</table>";
		return $html;
	}
}
?>