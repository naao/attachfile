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
	// echo '['.$mydirname.'_'.$module_dirname.'_'.$mytrustdirname.'_'.$targettrustdirname.'_'.$target_id.']' ;

	global $config_handler , $module_handler , $xoopsUser , $xoopsDB ;

	// emulate d3forum
	$mytrustdirname = $targettrustdirname ;
	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/'.$targettrustdirname ;
	$mydirname = $module_dirname ;

	$xoopsModule =& $module_handler->getByDirname( $module_dirname );
	if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar( 'hasnotification' ) == 1) {
		$xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
	}

	include_once $mytrustdirpath.'/include/main_functions.php' ;

	// from include/common_prepend.php
	// GET $uid
	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;
	$isadmin = $uid > 0 ? $xoopsUser->isAdmin() : false ;
	// get this user's permissions as perm array
	$category_permissions = d3forum_get_category_permissions_of_current_user( $mydirname ) ;
	$whr_read4cat = 'c.`cat_id` IN (' . implode( "," , array_keys( $category_permissions ) ) . ')' ;
	$forum_permissions = d3forum_get_forum_permissions_of_current_user( $mydirname ) ;
	$whr_read4forum = 'f.`forum_id` IN (' . implode( "," , array_keys( $forum_permissions ) ) . ')' ;

	// from main/edit.php
	$post_id = intval( $target_id ) ;
	// get this "post" from given $post_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
	if( ! $prs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $prs ) <= 0 ) return false ;
	$post_row = $xoopsDB->fetchArray( $prs ) ;
	$topic_id = intval( $post_row['topic_id'] ) ;

	// from include/process_this_topic.inc.php
	// get this "topic" from given $topic_id
	$sql = "SELECT t.*,u2t.u2t_time,u2t.u2t_marked,u2t.u2t_rsv,p.number_entity,p.special_entity FROM ".$xoopsDB->prefix($mydirname."_topics")." t LEFT JOIN ".$xoopsDB->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$xoopsDB->prefix($mydirname."_posts")." p ON t.topic_first_post_id=p.post_id WHERE t.topic_id=$topic_id" ;
	if( ! $trs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $trs ) <= 0 ) return false ;
	$topic_row = $xoopsDB->fetchArray( $trs ) ;
	$forum_id = intval( $topic_row['forum_id'] ) ;
	$isadminormod = (boolean) @$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
	// TOPIC_INVISIBLE (check & make where)
	if( $isadminormod ) {
		$whr_topic_invisible = '1' ;
	} else {
		if( $topic_row['topic_invisible'] ) return false ;
		$whr_topic_invisible = '! topic_invisible' ;
	}

	// from include/process_this_forum.inc.php
	// get this "forum" from given $forum_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_forums")." f WHERE ($whr_read4forum) AND f.forum_id=$forum_id" ;
	if( ! $frs = $xoopsDB->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	if( $xoopsDB->getRowsNum( $frs ) <= 0 ) return false ;
	$forum_row = $xoopsDB->fetchArray( $frs ) ;
	$cat_id = intval( $forum_row['cat_id'] ) ;
	$isadminormod = (boolean)$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
	$can_post = (boolean)$forum_permissions[ $forum_id ]['can_post'] || $isadminormod ;
	$can_edit = (boolean)$forum_permissions[ $forum_id ]['can_edit'] || $isadminormod ;
	$can_delete = (boolean)$forum_permissions[ $forum_id ]['can_delete'] || $isadminormod ;
	$need_approve = ! (boolean)$forum_permissions[ $forum_id ]['post_auto_approved'] && ! $isadminormod ;

	// from include/process_this_category.inc.php
	// get this "category" from given $cat_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_categories")." c WHERE $whr_read4cat AND c.cat_id=$cat_id" ;
	if( ! $crs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $crs ) <= 0 ) return false ;
	$cat_row = $xoopsDB->fetchArray( $crs ) ;
	$isadminorcatmod = (boolean)$category_permissions[ $cat_id ]['is_moderator'] || $isadmin ;
	$can_makeforum = (boolean)$category_permissions[ $cat_id ]['can_makeforum'] ;

	// from main/edit.php
	// hidden_uid
	if( $uid == $post_row['uid_hidden'] ) $post_row['uid'] = $post_row['uid_hidden'] ;

	// from include/process_eachpost.inc.php
	// get this poster's object
	$user_handler =& xoops_gethandler( 'user' ) ;
	$poster_obj =& $user_handler->get( intval( $post_row['uid'] ) ) ;
	if( is_object( $poster_obj ) ) {
		// active user's post
		// permissions
		$can_reply = ( $topic_row['topic_locked'] || $post_row['invisible'] || ! $post_row['approval'] ) ? false : $can_post ;
		if( $isadminormod ) {
			$can_edit = true ;
			$can_delete = true ;
		} else if( $post_row['uid'] == $uid ) {
			$can_edit = $forum_permissions[ $forum_id ]['can_edit'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfeditlimit'] ? true : false ;
			$can_delete = $forum_permissions[ $forum_id ]['can_delete'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfdellimit'] ? true : false ;
		} else {
			$can_edit = false ;
			$can_delete = false ;
		}
	} else {
		// guest or quitted or hidden user's post
		// permissions
		$can_reply = ( $topic_row['topic_locked'] || $post_row['invisible'] || ! $post_row['approval'] ) ? false : $can_post ;
		if( $isadminormod ) {
			$can_edit = true ;
			$can_delete = true ;
		} else if( $post_row['uid_hidden'] && $post_row['uid_hidden'] == $uid  ) {
			$can_edit = $forum_permissions[ $forum_id ]['can_edit'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfeditlimit'] ? true : false ;
			$can_delete = $forum_permissions[ $forum_id ]['can_delete'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfdellimit'] ? true : false ;
		} else {
			$can_edit = false ;
			$can_delete = false ;
		}
	}

	// from main/edit.php
	// check edit permission
	if( empty( $can_edit ) ) return false ;

	// check edit permission
	if( ! $uid ) {
		// guest edit (TODO)
		return false ;
	} else if( $isadminormod ) {
		// admin edit
		// ok
	} else if( $uid == $post_row['uid'] && $xoopsModuleConfig['selfeditlimit'] > 0 ) {
		// self edit
		if( time() < $post_row['post_time'] + intval( $xoopsModuleConfig['selfeditlimit'] ) ) {
			// before time limit
			// all green for self edit
		} else {
			// after time limit
			return false ;
		}
	} else {
		// no perm
		return false ;
	}

	return true;
}

function attachfile_check_download_permission_plugin( $mydirname , $module_dirname , $mytrustdirname , $targettrustdirname , $target_id )
{
	// echo '['.$mydirname.'_'.$module_dirname.'_'.$mytrustdirname.'_'.$targettrustdirname.'_'.$target_id.']' ;

	global $xoopsUser , $xoopsDB ;

	// emulate d3forum
	$mytrustdirname = $targettrustdirname ;
	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/'.$targettrustdirname ;
	$mydirname = $module_dirname ;

	include_once $mytrustdirpath.'/include/main_functions.php' ;

	// from include/common_prepend.php
	// GET $uid
	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;
	$isadmin = $uid > 0 ? $xoopsUser->isAdmin() : false ;
	// get this user's permissions as perm array
	$category_permissions = d3forum_get_category_permissions_of_current_user( $mydirname ) ;
	$whr_read4cat = 'c.`cat_id` IN (' . implode( "," , array_keys( $category_permissions ) ) . ')' ;
	$forum_permissions = d3forum_get_forum_permissions_of_current_user( $mydirname ) ;
	$whr_read4forum = 'f.`forum_id` IN (' . implode( "," , array_keys( $forum_permissions ) ) . ')' ;

	// from include/viewpost.php
	$post_id = intval( $target_id ) ;
	// get this "post" from given $post_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
	if( ! $prs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $prs ) <= 0 ) return false ;
	$post_row = $xoopsDB->fetchArray( $prs ) ;
	$topic_id = intval( $post_row['topic_id'] ) ;

	// from include/process_this_topic.inc.php
	// get this "topic" from given $topic_id
	$sql = "SELECT t.*,u2t.u2t_time,u2t.u2t_marked,u2t.u2t_rsv,p.number_entity,p.special_entity FROM ".$xoopsDB->prefix($mydirname."_topics")." t LEFT JOIN ".$xoopsDB->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$xoopsDB->prefix($mydirname."_posts")." p ON t.topic_first_post_id=p.post_id WHERE t.topic_id=$topic_id" ;
	if( ! $trs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $trs ) <= 0 ) return false ;
	$topic_row = $xoopsDB->fetchArray( $trs ) ;
	$forum_id = intval( $topic_row['forum_id'] ) ;
	$isadminormod = (boolean) @$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
	// TOPIC_INVISIBLE (check & make where)
	if( $isadminormod ) {
		$whr_topic_invisible = '1' ;
	} else {
		if( $topic_row['topic_invisible'] ) return false ;
		$whr_topic_invisible = '! topic_invisible' ;
	}

	// from include/process_this_forum.inc.php
	// get this "forum" from given $forum_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_forums")." f WHERE ($whr_read4forum) AND f.forum_id=$forum_id" ;
	if( ! $frs = $xoopsDB->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	if( $xoopsDB->getRowsNum( $frs ) <= 0 ) return false ;
	$forum_row = $xoopsDB->fetchArray( $frs ) ;
	$cat_id = intval( $forum_row['cat_id'] ) ;
	$isadminormod = (boolean)$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
	$can_post = (boolean)$forum_permissions[ $forum_id ]['can_post'] || $isadminormod ;
	$can_edit = (boolean)$forum_permissions[ $forum_id ]['can_edit'] || $isadminormod ;
	$can_delete = (boolean)$forum_permissions[ $forum_id ]['can_delete'] || $isadminormod ;
	$need_approve = ! (boolean)$forum_permissions[ $forum_id ]['post_auto_approved'] && ! $isadminormod ;

	// from include/process_this_category.inc.php
	// get this "category" from given $cat_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix($mydirname."_categories")." c WHERE $whr_read4cat AND c.cat_id=$cat_id" ;
	if( ! $crs = $xoopsDB->query( $sql ) ) return false ;
	if( $xoopsDB->getRowsNum( $crs ) <= 0 ) return false ;
	$cat_row = $xoopsDB->fetchArray( $crs ) ;
	$isadminorcatmod = (boolean)$category_permissions[ $cat_id ]['is_moderator'] || $isadmin ;
	$can_makeforum = (boolean)$category_permissions[ $cat_id ]['can_makeforum'] ;

	return true;
}

?>
