<?php
	/*
	 *	2014年12月18日2:57:22
	 *  模板类
	 */
	class Tpl{
		private static $tpl_value = array();
		function __construct(){

		}

		/*
		 *	2014年12月18日2:11:10
		 *	显示模板文件
		 *	$tpl_file: 模板文件, $suffix: 模板文件后缀名， $module: 模块名称
		 */
		public static function show($tpl_file, $suffix = 'php', $module = 'home'){
			if(!in_array($suffix, array('php', 'html', 'html'))){
				echo '文件后缀错误，请检查';
				exit;
			}
			if(!in_array($module, array('admin', 'home'))){
				echo '项目模块并不存在，请检查';
				exit;
			}

			$tpl_path = $module == 'home' ? HV_PATH . $tpl_file . "." . $suffix : AV_PATH . $tpl_file . "." .$suffix;
			if(!file_exists($tpl_path)){
				echo '模板文件不存在，请检查';
				exit;
			}
			$tpl_data = self::$tpl_value;
			include_once $tpl_path;
		}

		/*
		 *	2014年12月18日2:40:03
		 *	传递参数
		 *	$valName: 键名， $data: 键值
		 */
		public static function deliver($valName, $data){
			self::$tpl_value[$valName] = $data;
		}
	}