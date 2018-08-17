<?php 
namespace qdebug\trace;
use qing\utils\Instance;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
abstract class Trace implements TraceInterface{
	/**
	 * @return $this
	 * @return Node
	 * @return Console
	 * @return Backtrace
	 * @return Base
	 */
	public static function sgt(){
		return Instance::sgt(get_called_class());
	}
	/**
	 * @param string $v
	 * @return string
	 */
	public function color_red($v){
		return " <b style='color:red;'>{$v}</b> ";
	}
}
?>