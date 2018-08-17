<?php
namespace qdebug\controller;
use qing\filesystem\FileInfo;
use qing\webstatic\Header;
/**
 * @author xiaowang <736523132@qq.com>
 * @copyright Copyright (c) 2013 http://qingmvc.com
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
class Statics extends Base{
	/**
	 */
	public function index(){
		$file=$_GET['file'];
		$filename=__DIR__.'/../static/'.$file;
		$filename=realpath($filename);
		if(!$filename){
			return '';
		}
		$ext=FileInfo::ext($file);
		$ext=strtolower($ext);
		if($ext=='js'){
			Header::js();
		}else{
			Header::css();
		}
		echo file_get_contents($filename);
	}
}
?>