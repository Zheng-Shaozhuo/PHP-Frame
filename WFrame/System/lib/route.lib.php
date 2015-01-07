<?php
	/*
	 *	2014年12月17日19:58:45
	 *	路由类
	 *
	 */
	$obj = new route();
	$obj = $obj->run();

	class Route{
		private $cout;
		private $func;

		function __construct(){

		}

		/*
		 *	2014年12月17日20:06:08
		 *	跳转url
		 */
		public static function R($control = "index", $func = "index", $module = "home"){
			header("location:" . ($module == 'home' ? "index" : "admin") . ".php?cu=" . $control . "&fc=" . $func);
		}

		/*
		 *	2014年12月18日12:13:11
		 *	生成url
		 */
		public static function U($control = "index", $func = "index", $module = "home"){
			echo ($module == 'home' ? "index" : "admin") . ".php?cu=" . $control . "&fc=" . $func;
		}

		/*
		 *	2014年12月17日20:13:36
		 *	获取及解析
		 */
		public function run(){
			$this->cout = isset($_REQUEST['cu']) ? $_REQUEST['cu'] : 'index';
			$this->func = isset($_REQUEST['fc']) ? $_REQUEST['fc'] : 'index';

			$this->func = isset($this->func) && !empty($this->func) ? $this->func : "index";	

			$tmpControlPath = HC_PATH . $this->cout . 'Control';
			$tmpControl = $this->cout . 'Control';	
			require_once $tmpControlPath . '.php';
			$obj = new $tmpControl();
			$obj->fc = $this->func;
			$obj->run();
		}
	}