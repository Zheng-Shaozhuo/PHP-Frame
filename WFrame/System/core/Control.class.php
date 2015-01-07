<?php
	/*
		2014年12月17日0:46:13
		control控制器类
	 */

	class Control{
		private $fc;	//函数名称

		/*
		 *	构造函数
		 */
		function __construct(){
			self::check();
		}

		/*
		 *	私有属性赋值
		 */
		public function __set($key, $value){
			$this->$key = $value;
		}

		/*
		 *	获取私有属性值
		 */
		public function __get($key){
			return isset($this->$key) ? $this->$key : "";
		}

		/*
		 * 执行函数
		 */
		public function run(){
			$tmp = $this->fc;
			$this->$tmp();
		}
		/*
		 *	检索
		 */
		public function check(){

		}
	}