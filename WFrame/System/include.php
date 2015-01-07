<?php
	/*
		2014年12月16日23:48:34
		设置include_path
	 */
	$path = get_include_path().PATH_SEPARATOR.CORE_PATH.PATH_SEPARATOR.FUNC_PATH.PATH_SEPARATOR.CFG_PATH;
	set_include_path($path);

	require_once 'Control.class.php';
	require_once 'Model.class.php';
	require_once 'Tpl.class.php';

	require_once 'mysql.lib.php';
	require_once 'string.lib.php';
	require_once 'image.lib.php';
	require_once 'route.lib.php';
	require_once 'upload.lib.php';
