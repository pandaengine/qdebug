<?php 
namespace qdebug\trace;
/**
 * 
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
interface TraceInterface{
	/**
	 *
	 * @return string
	 */
	public function title();
	/**
	 *
	 * @return string
	 */
	public function log();
	/**
	 * @param array $data
	 */
	public function format(array $data);
}
?>