<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'attachfile' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","�ե�����ź��");

// A brief description of this module
define($constpref."_DESC","XOOPS�ե�����ź�ե⥸�塼��");

// admin menus
define($constpref.'_ADMENU_LIST','ź�եե��������');

// configurations
define($constpref.'_LINK_ENC','����ɽ�����Υ����ȥ륨�󥳡��ǥ���');
define($constpref.'_LINK_ENCDSC','����ɽ�����Υե�����̾�Υ��󥳡��ǥ���');
define($constpref.'_TTL_ENC_IE','��������ɻ��Υ����ȥ륨�󥳡��ǥ��󥰡�MSIE��');
define($constpref.'_TTL_ENC_IEDSC','��������ɻ��˥��饤����Ȥ��Ϥ��ե�����̾�Υ��󥳡��ǥ���');
define($constpref.'_TTL_ENC_OTH','��������ɻ��Υ����ȥ륨�󥳡��ǥ��󥰡�MSIE�ʳ���');
define($constpref.'_TTL_ENC_OTHDSC','��������ɻ��˥��饤����Ȥ��Ϥ��ե�����̾�Υ��󥳡��ǥ���');
define($constpref.'_MAX_SIZE','ź�եե�������祵����(KB)');
define($constpref.'_MAX_SIZEDSC','�ҤȤĤ�ź�եե�����κ��祵����');
define($constpref.'_MIMEM','���åץ���MIME-Type���¥⡼��');
define($constpref.'_MIMEMDSC','�ʤ� : ̵����, ���� : ���åץ���MIME-Type�ꥹ�ȤΤ�Τ����, ���� : ���åץ���MIME-Type�ꥹ�ȤΤ�ΤΤߵ���');
define($constpref.'_MIMET','���åץ���MIME-Type�ꥹ��');
define($constpref.'_MIMETDSC','"|"�Ƕ��ڤäƤ����������㡧"text/plain|image/gif|image/jpeg"');
define($constpref.'_F_PRE','�¥ե�����̾�ץ�ե�����');
define($constpref.'_F_PREDSC','XOOPS_TRUST_PATH��Υե�����Υץ�ե�����(���˥ե����뤬¸�ߤ��Ƥ�����֤Ǥ����ͤ��ѹ�������硢��ư�Ǵ�¸�Υե�����̾���ѹ�����ɬ�פ�����ޤ�(DB�˳�Ǽ���줿�ե�����̾���ѹ�����ɬ�פϤ���ޤ���))');

define($constpref.'_MIMEM_NON_N','�ʤ�');					// 0
define($constpref.'_MIMEM_DNY_N','����');					// 1
define($constpref.'_MIMEM_ALW_N','����');					// 2
define($constpref.'_F_PRE_NON_N','�ʤ�');					// 0
define($constpref.'_F_PRE_DBP_N','XOOPS_DB_PREFIX��Ʊ��');	// 1
define($constpref.'_F_PRE_DBN_N','XOOPS_DB_NAME��Ʊ��');	// 2

}

?>