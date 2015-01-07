<?php
	header("content-type:text/html;charset=utf-8");	//设定项目编码格式
	/*
	 *	2014年12月18日1:29:46
	 *	图片处理类
	 *
	 */
	class Image{
		function __construct(){

		}

		/*
		 *	2014年12月18日1:31:01
		 *	生成验证码
		 *	
		 */
		public static function verifyImage($width = 80, $height = 28, $type = 1, $len = 4, $sess_name = 'verify', $pixels = 0, $lines = 0){
			$image = imagecreatetruecolor($width, $height);		//创建画布
			$white = imagecolorallocate($image, 255, 255, 255);	//白色
			$black = imagecolorallocate($image, 0, 0, 0);		//黑色

			imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $white);	//白色填充画布
			$verifyStr = string::randString($type, $len);
			$_SESSION[$sess_name] = $verifyStr;					//验证码写入session

			//绘制验证码
			for($i = 0; $i < $len; $i++){
				$size 		= mt_rand($height - 14, $height - 8);
				$angle 		= mt_rand(-30, 30);
				$x			= mt_rand(4, 7) + $i * $size;
				$y			= mt_rand($height - 10, $height - 4);
				$color 		= imagecolorallocate($image, mt_rand(40, 100), mt_rand(80, 200), mt_rand(100, 200));
				$fontfile	= APATH_FONT . 'Candara.ttf';
				$text		= substr($verifyStr, $i, 1);
				imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
			}
			//绘制干扰点
			if($pixels){
				for($i = 0; $i < $pixels; $i++){
					$color 	= imagecolorallocate($image, mt_rand(40, 100), mt_rand(80, 200), mt_rand(100, 200));
					imagesetpixel($image, mt_rand(0, $width - 1), mt_rand(0, $height - 1), $color);
				}
			}
			if($lines){
				for($i = 0; $i < $lines; $i++){
					$color 	= imagecolorallocate($image, mt_rand(40, 100), mt_rand(80, 200), mt_rand(100, 200));
					imageline($image, mt_rand(40, 100), mt_rand(80, 200), mt_rand(40, 100), mt_rand(80, 200), mt_rand(100, 200));
				}
			}
			header("content-type: image/jpeg");
			imagejpeg($image);
			imagedestroy($image);
		}

		/*
		 *	2014年12月19日21:04:14
		 *	图片处理之生成缩略图
		 *	$filename: 文件名， $path: 文件路径，$prefix：新图片前缀，$scale：压缩比率， $dst_w：目标文件宽， $dst_h：目标文件高
		 */
		public static function thumbImage($filename, $path = '', $prefix = 'small', $scale = 0.8, $dst_w = null, $dst_h = null){
			if(is_null($filename) || !file_exists($filename)){
				return array('flag' => false, 'errinfo' => '该文件不存在', 'image' => '');
			}
			if($path == '' || is_dir($path)){
				$path = $path == '' ? $path : $path . '/';
			}else{
				return array('flag' => false, 'errinfo' => '文件保存路径错误', 'image' => '');
			}

			//getimagesize 取得图像大小
			list($src_w, $src_h, $imagetype) = getimagesize($filename);

			//image_type_to_mime_type 返回的图像类型的 MIME 类型
			$mime = image_type_to_mime_type($imagetype);

			//imagecreateform*** 由文件或 URL 创建一个新图象
			$createFun = str_replace('/', 'createfrom', $mime);
			$src_image = $createFun($path . $filename);

			if(is_null($dst_w) || is_null($dst_h))
			$dst_w = !is_null($dst_w) && !is_null($dst_h) ? $dst_w : ceil($src_w * $scale);
			$dst_h = !is_null($dst_h) && !is_null($dst_h) ? $dst_h : ceil($src_h * $scale);

			//imagecreatetruecolor 新建一个真彩色图像
			$dst_image = imagecreatetruecolor($dst_w, $dst_h);
			
			//imagecopyresampled 重采样拷贝部分图像并调整大小
			imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);	

			$outFun = str_replace('/', null, $mime);
			$outFun($dst_image, $path . $prefix . "_" .$filename);

			imagedestroy($src_image);
			imagedestroy($dst_image);

			return array('flag' => true, 'errinfo' => '缩略图生成成功', 'image' => $path . $prefix . "_" .$filename);
		}

		/*
		 *	2014年12月19日22:06:24
		 *	添加文字水印
		 *	$filename:图片文件，$path:保存路径，$text:水印文字，$size:字体大小，$angle:角度，$pos:水印位置，$rgba:文字颜色，$fontfile:字体，$padding:内边距
		 */
		public static function waterText($filename, $path = '', $text = 'this is my test', $size = 22, $angle = 0, $pos = 1, $padding = 6, $rgba = array(0, 0, 0, 0.5), $fontfile = null){
			if(is_null($filename) || !file_exists($filename)){
				return array('flag' => false, 'errinfo' => '该文件不存在', 'image' => '');
			}
			if(!in_array($pos, array(1, 2, 3, 4, 5))){
				return array('flag' => false, 'errinfo' => '参数错误', 'image' => '');
			}
			if($path == '' || is_dir($path)){
				$path = $path == '' ? $path : $path . '/';
			}else{
				return array('flag' => false, 'errinfo' => '文件保存路径错误', 'image' => '');
			}

			//采集文件信息
			$imageInfo = getimagesize($filename);
			list($src_w, $src_h) = getimagesize($filename);
			//获得文件MIME类型
			$mime = $imageInfo['mime'];
			//创建一个新图像
			$createFun = str_replace('/', 'createfrom', $mime);
			$outFun = str_replace('/', null, $mime);

			$image = $createFun($filename);
			$ww = mb_strlen($text) / 1.7 * $size;
			switch($pos){
				case 1:			//左上角
					$x = $padding;
					$y = $size + $padding;
					$angle = 0;
					break;
				case 2:			//左下角
					$x = $padding;
					$y = $src_h - $padding;
					$angle = 0;
					break;
				case 3:			//右下角
					$x = $src_w - $ww - $padding;
					$y = $src_h - $padding;
					$angle = 0;
					break;
				case 4:			//右上角
					$x = $src_w - $ww - $padding;
					$y = $size + $padding;
					$angle = 0;
					break;
				case 5:			//中间
					$x = ($src_w - $ww * cos($angle)) / 2;
					$y = ($src_h - $ww * sin($angle)) / 2;
					break;
			}

			//生成文字颜色
			$color = imagecolorallocatealpha($image, $rgba[0], $rgba[1], $rgba[2], ceil(($rgba[3] >= 1 ? 1 : $rgba[3]) * 127));
			$fontfile = is_null($fontfile) ? APATH_FONT . 'Candara.ttf'; : $fontfile;

			//图片上绘制文字
			imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
			//保存文件
			$outFun($image, $path . $filename);

			//删除资源
			imagedestroy($image);

			return array('flag' => true, 'errinfo' => '文字水印生成成功', 'image' => $path . $filename);
		}

		/*
		 *	2014年12月19日22:23:12
		 *	添加图片水印
		 *
		 */
		public static function waterPic($waterFile, $dstFile, $path, $dstName, $pct = 1, $pos = 1){
			if(is_null($waterFile) || is_null($dstFile) || !file_exists($waterFile) || !file_exists($dstFile)){
				return array('flag' => false, 'errinfo' => '该文件不存在', 'image' => '');
			}
			if(is_null($path) || is_null($dstName) || !in_array($pos, array(1, 2, 3, 4, 5))){
				return array('flag' => false, 'errinfo' => '参数错误', 'image' => '');
			}
			if($path == '' || is_dir($path)){
				$path = $path == '' ? $path : $path . '/';
			}else{
				return array('flag' => false, 'errinfo' => '文件保存路径错误', 'image' => '');
			}

			$waterFileInfo = getimagesize($waterFile);
			$dstFiileInfo = getimagesize($dstFile);

			list($water_w, $water_h) = getimagesize($waterFile);
			list($dst_w, $dst_h) = getimagesize($dstFile);

			$waterMime = $waterFileInfo['mime'];
			$dstFileMime = $dstFiileInfo['mime'];

			$extension = substr($dstFile, strrpos($dstFile, '.') + 1);

			$createWaterFun = str_replace('/', 'createfrom', $waterMime);
			$createDstFun = str_replace('/', 'createfrom', $dstFileMime);

			$outFun = str_replace('/', null, $dstFileMime);

			$src_im = $createWaterFun($waterFile);
			$dst_im = $createDstFun($dstFile);

			switch($pos){
				case 1:		//左上角
					$dst_x = 0;
					$dst_y = 0;
					break;
				case 2:		//左下角
					$dst_x = 0;
					$dst_y = $dst_h - $water_h;
					break;
				case 3:		//右下角
					$dst_x = $dst_w - $water_w;
					$dst_y = $dst_h - $water_h;
					break;
				case 4:		//右上角
					$dst_x = $dst_w - $water_w;
					$dst_y = 0;
					break;
				case 5:		//中间
					$dst_x = ($dst_w - $water_w) / 2;
					$dst_y = ($dst_h - $water_h) / 2;
					break;
				default:
					break;	
			}
			//imagecopymerge 拷贝并合并图像的一部分
			imagecopymerge($dst_im, $src_im, $dst_x, $dst_y, 0, 0, $water_w, $water_h, ($pct >= 1 ? 1 : $pct) * 100);

			$outFun($dst_im, $path . $dstName . "." . $extension);
			imagedestroy($src_im);
			imagedestroy($dst_im);

			return array('flag' => true, 'errinfo' => '图片水印生成成功', 'image' => $path . $dstName . "." . $extension);
		}
	}
	
	var_dump(Image::waterText('123.jpg'));
	//var_dump(Image::waterPic('logo.png', '123.jpg', '', '234', 0.8, 5));
	//var_dump(Image::thumbImage('123.jpg', '', 'ss', 0.5));