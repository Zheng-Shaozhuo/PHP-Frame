<?php
	/*
	 *	2014年12月20日11:18:55
	 *	文件校验类
	 *	2014年12月20日12:41:39
	 */
	class Validate{
		function __construct(){

		}

		/*
		 *	2014年12月20日11:20:40
		 *	校验邮件
		 *	$string: email地址
		 */
		public static function email($string){
			if(is_null($string)){
				return false;
			}

			$pattern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
			return preg_match($pattern, $string) ? true : false; 

			// return filter_var($string, FILTER_SANITIZE_EMAIL) ? true : false;
		}

		/*
		 *	2014年12月20日11:28:32
		 * 	校验QQ号码
		 *	$string:QQ号码， $len:长度
		 */
		public static function qq($string, $len = null){
			if(is_null($string)){
				return false;
			}
			$len = is_null($len) ? 10 : $len - 1;
			$pattern = "/^[1-9]{1}\d{4,{$len}}$/";

			return preg_match($pattern, $string) ? true : false; 
		}

		/*
		 *	2014年12月20日11:34:32
		 *	检验手机号码
		 *	$string:手机号码
		 */
		public static function phone($string){
			if(is_null($string)){
				return false;
			}

			$pattern = "/1[3458]{1}\d{9}/";
			return preg_match($pattern, $string) ? true : false;
		}

		/*
		 *	2014年12月20日11:42:20
		 *	校验身份证号码
		 *	$string:身份证号码
		 */
		public static function idCard($string){
			if(is_null($string) || (strlen($string) != 15 && strlen($string) != 18)){
				return false;
			}
			if(strlen($string) == 15){
				$string = substr($string, 0, 6) . "19" . substr($string, 7) . "x";	
			}
			
			$pattern = "/^[1-6]{1}[0-9]{1}\d{4}[19|20]{2}[0-9]{2}[0-1]{1}[0-9]{1}[0-3]{1}[0-9]{1}[0-9]{3}[0-9|x]{1}$/";
			return preg_match($pattern, $string) ? true : false;
		}

		/*
		 *	2014年12月20日12:19:13
		 *	校验IP
		 *	$string: ip
		 */
		public static function ip($string){
			if(is_null($string)){
				return false;
			}

			// $pattern = "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/";
			// return preg_match($pattern, $string) ? true : false;

			return filter_var($string, FILTER_VALIDATE_IP) ? true : false;
		}

		/*
		 *	2014年12月20日12:22:07
		 *	校验邮政编码
		 *	$string: 邮政编码
		 */
		public static function postal($string){
			if(is_null($string) || strlen($string) != 6){
				return false;
			}

			$pattern = "/^[1-9]\d{5}$/";
			return preg_match($pattern, $string) ? true : false;
		}

		/*
		 *	2014年12月20日12:31:07
		 *	校验url
		 *	$string: url
		 */
		public static function url($string){
			
			if(is_null($string)){
				return false;
			}

			// $pattern = "/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/";
			// return preg_match($pattern, $string) ? true : false;

			return filter_var($string, FILTER_SANITIZE_URL) ? true : false;
		}
	}
