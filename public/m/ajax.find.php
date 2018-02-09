<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

//$_POST = utf8ToEuckr($_POST);

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

$id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];
$name = ($_POST['name']) ? $_POST['name'] : $_GET['name'];
$mail = ($_POST['mail']) ? $_POST['mail'] : $_GET['mail'];

switch($mode){
	case 'findId':
		if($name && $mail){
			
			list($result)=pmysql_fetch_array(pmysql_query("SELECT id FROM tblmember WHERE name = '".iconv("utf-8","euc-kr",$name)."' AND email = '".$mail."'"));
		}
		
		
	break;
	case 'findPw':
	
		list($mem_id,$mem_mail,$dupinfo)=pmysql_fetch_array(pmysql_query("select id,email,dupinfo from tblmember where id='".iconv("utf-8","euc-kr",$id)."' and name='".iconv("utf-8","euc-kr",$name)."' and email='".$mail."'"));

		
		$passwd=substr(md5(rand(0,9999999)),0,8);

		if($mem_id){
		
			$sql = "UPDATE tblmember SET passwd='".md5($passwd)."' ";
			$sql.= "WHERE id='".iconv("utf-8","euc-kr",$id)."' AND name='".iconv("utf-8","euc-kr",$name)."' and email='".$mem_mail."'";

			if(pmysql_query($sql,get_db_conn())){
				SendPassMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->info_email, $mem_mail, iconv("utf-8","euc-kr",$name), $mem_id, $passwd);
				//SMS 발송
				sms_autosend( 'mem_passwd', $mem_id, '', $passwd );
				//SMS 관리자 발송
				sms_autosend( 'admin_passwd', $mem_id, '', $passwd );
				$result="ok";
				
			}
		}
				
	break;
}

echo $result;
?>