<?php 
	/*
	 *	2014年12月17日12:14:25
	 *	
	 */

	class indexControl extends Control {
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			// Tpl::deliver('info', array(1 => 'msing', 2 => 'breath'));
			// Tpl::show('index', 'php', 'home');
			$model = new Model();
			$result = $model->table('mm')->delete();
			var_dump($model->get_last_query());
			var_dump($result);
		}

		public function mytest(){
			echo 'this is my tesst';
		}

		public function mypost(){
			var_dump($_POST);
		}
	}