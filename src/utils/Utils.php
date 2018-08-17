<?php
namespace qdebug\utils;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Utils{
	/**
	 * @return string
	 */
	public static function uri(){
		//$uri=Request::getRequestUri();
		$query=(string)@$_SERVER['QUERY_STRING'];
		$uri=(string)@$_SERVER['PATH_INFO'];
		if(!$uri){
			$uri='首页';
		}
		if($query){
			$uri=$uri.'?'.$query;
		}
		return $uri;
	}
}
?>