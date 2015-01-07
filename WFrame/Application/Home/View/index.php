<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php var_dump($tpl_data['info']); ?>	

	<img src="index.php?cu=list&fc=verifyImage">
	<input type="button" value="测试跳转" onclick="window.location.href='<?php Route::U('index', 'mytest');?>'">

	<form action="<?php Route::U('index', 'mypost'); ?>" method="post">
		输入测试数据 <input type="text" name="test" value="" placeholder="请输入测试数据">
		<input type="submit" value="提交">
	</form>	
</body>
</html>