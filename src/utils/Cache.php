<?php
namespace qdebug\utils;
use qing\filesystem\MK;
//use qing\utils\Instance;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Cache{
	/**
	 * @return string
	 */
	public static function cachePath(){
		return APP_RUNTIME."/~qdebug";
	}
	/**
	 * @param string $id
	 * @return string
	 */
	public static function cacheFile($id){
		return self::cachePath()."/{$id}.log";
	}
	/**
	 * @param array $data
	 */
	public static function set(array $data){
		$uri=Utils::uri();
		$id =md5($uri.$_SERVER['REQUEST_TIME_FLOAT']);
		//追加会话
		self::set_session($id,$uri);
		//保存数据
		$cacheFile=self::cacheFile($id);
		MK::dir(dirname($cacheFile));
		file_put_contents($cacheFile,self::encodeValue($data));
	}
	/**
	 * @param array $data
	 */
	protected static function encodeValue(array $data){
		return '<?php return '.var_export($data,true).' ?>';
	}
	/**
	 * - __set_state静态方法，根据属性创建一个实例
	 * - 不支持该方法的类会出错
	 * 
	 * @param string $id
	 * @return array|multitype:
	 */
	public static function get($id=''){
		$cacheFile=self::cacheFile($id);
		if(is_file($cacheFile)){
			//剔除__set_state
			$changed=false;
			$content=file_get_contents($cacheFile);
			$content=preg_replace_callback('/([a-z0-9_\\\\]+)\:\:__set_state\(/i',function($matches)use(&$changed){
				$changed=true;
				$class=$matches[1];
				return __CLASS__."::_set_state('{$class}',";
			},$content);
			if($changed){
				file_put_contents($cacheFile,$content);
			}
			return (array)include $cacheFile;
		}else{
			return [];
		}
	}
	/**
	 * @param string $class
	 * @param array $data
	 */
	public static function _set_state($class,$data){
		return "Object({$class})";
		if(class_exists($class)){
			$obj=new $class();
			//Instance::setProps($obj,$data);
			return $obj;
		}else{
			return $class;
		}
	}
	/**
	 * @param string $id
	 * @param string $uri
	 */
	public static function set_session($id,$uri){
		$sessions=(array)self::get_sessions();
		$sessions[]=[$id,$uri];
		//最新的会话id
		file_put_contents(self::cacheFile('sessions'),self::encodeValue($sessions));
	}
	/**
	 */
	public static function get_sessions(){
		return self::get('sessions');
	}
}
?>