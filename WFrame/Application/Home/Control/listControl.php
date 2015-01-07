<?php
	class listControl extends Control{
		function __construct(){
			parent::__construct();
		}

		public function index(){
			Route::R("list", "verifyImage");
		}

		public function verifyImage(){
			Image::verifyImage();
		}
	}