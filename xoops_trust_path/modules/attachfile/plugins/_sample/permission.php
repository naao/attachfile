<?php

// ===========================================================================
// The permission file is necessary in each module.
// If target module is "D3 module", the permission file is necessary
// in each directory in XOOPS_TRUST_PATH (not XOOPS_ROOT_PATH).
//
// -- argument --
// 
// $mydirname			: attachfile's dirname in XOOPS_ROOT_PATH
// $module_dirname		: target module's dirname in XOOPS_ROOT_PATH
// $mytrustdirname		: attachfile's dirname in XOOPS_TRUST_PATH
// $targettrustdirname	: target module's dirname in XOOPS_TRUST_PATH
// $target_id			: target mosule's contents id (target to attach)
// 
// -- return value --
// 
// true					: allow access
// false				: deny access
// ===========================================================================

function attachfile_check_upload_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id )
{
	return true;
}

function attachfile_check_download_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id )
{
	return true;
}

?>
