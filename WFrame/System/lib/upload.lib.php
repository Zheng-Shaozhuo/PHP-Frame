<?php
	/*
	 *	2014年12月19日18:46:55
	 *	文件上传类
	 *
	 */
	class Upload{
		function __construct(){

		}

		/*
		 *	2014年12月19日18:48:07
		 *	单文件上传类
		 *	$file： 文件数组， $path: 保存路径， $array: 校验数组, $max_size: 文件大小
		 *
		 *	file_uploads = On, 支持HTTP POST方式上传
		 *	upload_tmp_dir 临时文件保存目录
		 * 	upload_max_filesize 上传默认大小
		 *  post_max_size post方式上传大小
		 */
		public function uploadFile($file, $path, $max_size = null, $array = null){
			if(!is_array($file) || !isset($file) || !is_string($path) || !isset($path)){
				return array('flag' => false, 'errinfo' => '参数错误');
			}

			if($file['error'] == UPLOAD_ERR_OK){
				$extension = String::getExtension($file['name']);

				if(is_array($array) && !in_array($extension, $array)){
					$errinfo = "上传文件类型错误";
				}
				if(is_array($array) && (in_array('jpg', $array) || in_array('png', $array) || in_array('gif', $array)) && !getimagesize($file['tmp_name'])){
					$errinfo = "伪装的图片文件，请检查";
				}
				if(isset($max_size) && $file['size'] > $max_size){
					$errinfo = "上传文件过大";
				}
				if(!file_exists($path)){
					$errinfo = "文件上传路径不存在";
				}

				//判断是否通过HTTP POST方式上传
				if(is_uploaded_file($file['tmp_name'])){
					$desfile = String::getUniString() . "." . $extension;
					if(move_uploaded_file($file['tmp_name'], $path . $desfile)){
						return array('flag' => true, 'errinfo' => '文件上传成功', 'file' => $path . $desfile);								
					}else{

					}	
				}else{
					$errinfo = "文件非正常路径上传";
				}
			}else{
				switch ($file['error']) {
					case 1:	//UPLOAD_ERR_INI_SIZE
						$errinfo = "超过配置文件上传大小";
						break;
					case 2:	//UPLOAD_ERR_FORM_SIZE
						$errinfo = "超过表单上传文件大小";
						break;
					case 3:	//UPLOAD_ERR_PARTIAL
						$errinfo = "文件部分被上传";
						break;
					case 4:	//UPLOAD_ERR_NO_FILE
						$errinfo = "没有文件没上传";
						break;
					case 5:
						$errinfo = "文件大小为 0 ";
						break;
					case 6:	//UPLOAD_ERR_NO_TMP_DIR
						$errinfo = "没有找到临时文件目录";
						break;
					case 7:	//UPLOAD_ERR_CANT_WRITE
						$errinfo = "文件不可写";
						break;
					case 8:	//UPLOAD_ERR_EXTENSION
						$errinfo = "由于PHP扩展终止文件上传";
						break;		
					default:
						break;
				}
			}
			return array('flag' => false, 'errinfo' => $errinfo, 'file' => '');
		}

		/*
		 *	2014年12月19日18:48:07
		 *	多文件上传
		 *	$files： 多文件数组， $path: 保存路径， $array: 校验数组, $max_size: 文件大小
		 *
		 */
		public function uploadMulFiles($files, $path, $max_size = null, $array = null){
			if(!is_array($files) || !isset($files) || !is_string($path) || !isset($path)){
				return array('flag' => false, 'errinfo' => '参数错误');
			}

			$result = array();
			foreach($this->dealMulFile($files) as $k => $val){	
			
				if($val['error'] == UPLOAD_ERR_OK){
					$extension = String::getExtension($val['name']);

					if(is_array($array) && !in_array($extension, $array)){
						$tmp =  array('flag' => false, 'errinfo' => '上传文件类型错误', 'val' => '');
					}
					if(is_array($array) && (in_array('jpg', $array) || in_array('png', $array) || in_array('gif', $array)) && !getimagesize($val['tmp_name'])){
						$tmp =  array('flag' => false, 'errinfo' => '伪装的图片文件，请检查', 'val' => '');
					}
					if(isset($max_size) && $val['size'] > $max_size){
						$tmp =  array('flag' => false, 'errinfo' => '上传文件过大', 'val' => '');
					}
					if(!file_exists($path)){
						$tmp =  array('flag' => false, 'errinfo' => '文件上传路径不存在', 'val' => '');
					}

					//判断是否通过HTTP POST方式上传
					if(is_uploaded_file($val['tmp_name'])){
						$desfile = String::getUniString() . "." . $extension;
						if(move_uploaded_file($val['tmp_name'], $path . $desfile)){
							$tmp =  array('flag' => true, 'errinfo' => '文件上传成功', 'val' => $path . $desfile);								
						}else{

						}	
					}else{
						$tmp =  array('flag' => false, 'errinfo' => '文件非正常路径上传', 'val' => '');
					}
				}else{
					switch ($val['error']) {
						case 1:	//UPLOAD_ERR_INI_SIZE
							$tmp =  array('flag' => false, 'errinfo' => '超过配置文件上传大小', 'val' => '');
							break;
						case 2:	//UPLOAD_ERR_FORM_SIZE
							$tmp =  array('flag' => false, 'errinfo' => '超过表单上传文件大小', 'val' => '');
							break;
						case 3:	//UPLOAD_ERR_PARTIAL
							$tmp =  array('flag' => false, 'errinfo' => '文件部分被上传', 'val' => '');
							break;
						case 4:	//UPLOAD_ERR_NO_FILE
							$tmp =  array('flag' => false, 'errinfo' => '没有文件没上传', 'val' => '');
							break;
						case 5:
							$tmp =  array('flag' => false, 'errinfo' => '文件大小为 0 ', 'val' => '');
							break;
						case 6:	//UPLOAD_ERR_NO_TMP_DIR
							$tmp =  array('flag' => false, 'errinfo' => '没有找到临时文件目录', 'val' => '');
							break;
						case 7:	//UPLOAD_ERR_CANT_WRITE
							$tmp =  array('flag' => false, 'errinfo' => '文件不可写', 'val' => '');
							break;
						case 8:	//UPLOAD_ERR_EXTENSION
							$tmp =  array('flag' => false, 'errinfo' => '由于PHP扩展终止文件上传', 'val' => '');
							break;		
						default:
							break;
					}
				}
				$result[$k] = $result;
			}
			return $result;
		}


		/*
		 *	2014年12月19日20:02:31
		 *	多文件上传分类信息
		 *	$data: 多文件数组
		 */
		public function dealMulFile($data){
			$i = 0;
			foreach($data as $v){
				//单文件
				if(is_string($v['name'])){
					$files[$i] = $v;
					$i++;
				}else{
					//多文件
					foreach($v['name'] as $k => $mv){
						$files[$i]['name'] = $mv;
						$files[$i]['size'] = $v['size'][$k];
						$files[$i]['tmp_name'] = $v['tmp_name'][$k];
						$files[$i]['error'] = $v['error'][$k];
						$files[$i]['type'] = $v['type'][$k];
						$i++;
					}	
				}				
			}
			return $files;
		}
	}	