<?php
	/*
	 *	2014年12月18日1:08:01
	 *	字符串处理类
	 */
	class String{
		function __construct(){

		}

		/*
		 *	2014年12月18日1:09:08
		 *	生成随机字符串
		 *	$type: 字符串类型， $len: 字符串长度
		 */
		public static function randString($type = 1, $len = 4){
			$str = "";
			switch ($type) {
				case 1:
					$str = join("", range(0, 9));
					break;	
				case 2:
					$str = join("", array_merge(range('a', 'z'), range('A', 'Z')));
					break;
				case 3:
					$str = join("", array_merge(range('a', 'z'), range('A', 'Z'), range(0, 1)));
					break;
				default:
					break;
			}
			if($len > strlen($str)){
				$str = str_repeat($str, ceil($len / strlen($str)));
			}

			return substr(str_shuffle($str), 0, $len);
		}

		/*
		 *	2014年12月19日19:10:41
		 *	返回唯一的字符串
		 *
		 */
		public static function getUniString(){
			//uniqid 获取一个带前缀、基于当前时间微秒数的唯一ID
			//mircotime 返回当前 Unix 时间戳和微秒数
			return md5(uniqid(microtime(true), true));
		}

		/*
		 *	2014年12月19日19:14:26
		 *	获取文件扩展名
		 *	$filename： 文件名
		 */
		public static function getExtension($filename){
			//end 指向数组内部的最后一个单元
			return strtolower(end(explode('.', $filename)));				
		}
	}