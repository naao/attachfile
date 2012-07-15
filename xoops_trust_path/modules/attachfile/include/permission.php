<?php

function attachfile_check_upload_permission( $mydirname , $module_dirname , $target_id )
{
	$mytrustdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$targettrustdirname = attachfile_get_targettrustdirname( $module_dirname ) ;

	$permission_inc = dirname( dirname( __FILE__ ) )."/plugins/$targettrustdirname/permission.php" ;
	if( ! file_exists( $permission_inc ) ) {
		return _MD_ATTACHFILE_ERR_PLUGINNOTFOUND.' ('.$targettrustdirname.')' ;
	}
	include_once $permission_inc ;
	if( ! attachfile_check_upload_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id ) ) {
		return _MD_ATTACHFILE_ERR_CANTUPLOAD.' ('.$module_dirname.')' ;
	}
	return null ;
}

function attachfile_check_download_permission( $mydirname , $module_dirname , $target_id )
{
	$mytrustdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$targettrustdirname = attachfile_get_targettrustdirname( $module_dirname ) ;

	$permission_inc = dirname( dirname( __FILE__ ) )."/plugins/$targettrustdirname/permission.php" ;
	if( ! file_exists( $permission_inc ) ) {
		return _MD_ATTACHFILE_ERR_PLUGINNOTFOUND.' ('.$targettrustdirname.')' ;
	}
	include_once $permission_inc ;
	if( ! attachfile_check_download_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id ) ) {
		return _MD_ATTACHFILE_ERR_CANTDOWNLOAD.' ('.$module_dirname.')' ;
	}
	return null ;
}

function attachfile_get_targettrustdirname( $module_dirname )
{
	$targettrustfile = XOOPS_ROOT_PATH.'/modules/'.$module_dirname.'/mytrustdirname.php' ;
	if( ! file_exists( $targettrustfile ) ) {
		// Maybe non "D3 module".
		return '' ;
	}
	// ** YOU DON'T INCLUDE THIS FILE IN OTHER FUNCTION OR GLOBAL AREA.
	// ** THIS FILE OVERWRITE $mytrustdirname IN attachfile MODULE.
	// ** YOU SHOULD INCLUDE THIS FILE ONLY IN LIMITED FUNCTION.
	include $targettrustfile ;
	return $mytrustdirname ;
}

?>
