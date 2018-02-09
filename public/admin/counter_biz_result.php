<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	include_once($Dir."data/bizConfig2.php");
/*
	if(!$_GET[id]){
		$f = fopen($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php","w");
		fwrite($f,"<?");
		fwrite($f,"\$biz = array('bizNumber'=>'".$_GET[id]."', 'bizId'=>'".$_GET[cusId]."', 'bizPassword'=>'');");
		fwrite($f,"?>");
		fclose($f);
		chmod($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php",0707);
	}else{		
		$insert = pmysql_query("DELETE FROM tblbizspring", get_db_conn())

		$f = fopen($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php","w");
		fwrite($f,"<?");
		fwrite($f,"\$biz = array('bizNumber'=>'', 'bizId'=>'', 'bizPassword'=>'');");
		fwrite($f,"?>");
		fclose($f);
		chmod($_SERVER[DOCUMENT_ROOT]."/data/bizConfig2.php",0707);
	}*/
?>