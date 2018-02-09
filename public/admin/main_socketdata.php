<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

################# SMS 잔여 #################
$sql = "SELECT id, authkey FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);
$sms_id=$row->id;
$sms_authkey=$row->authkey;

$sms_count="";
if(ord($sms_id)==0 || ord($sms_authkey)==0) {
	$sms_count="<A style=\"cursor:hand\" onclick=\"alert(\'SMS 회원가입 및 충전 후 SMS 기본환경 설정에서 SMS 아이디 및 인증키를 입력하시기 바랍니다.\');\"><font color=red><U>미셋팅!!</U></font></A>";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$sms_count="<font class=\"font_orange4\"><b>".substr($smscountdata,3)."</b></font> Point";
	} else if(substr($smscountdata,0,2)=="NO") {
		$sms_count="<A style=\"cursor:hand\" onclick=\"alert(\'SMS 회원 아이디가 존재하지 않습니다. SMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.\');\"><font class=\"font_orange4\"><B>인증오류!!</B></font></A>";
	} else if(substr($smscountdata,0,2)=="AK") {
		$sms_count="<A style=\"cursor:hand\" onclick=\"alert(\'SMS 회원 인증키가 일치하지 않습니다. SMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.\');\"><font class=\"font_orange4\"><B>인증오류!!</B></font></A>";
	} else {
		$sms_count="<A style=\"cursor:hand\" onclick=\"alert(\'SMS 서버와 통신이 불가능합니다. 잠시 후 이용하시기 바랍니다.\');\"><font class=\"font_orange4\"><B>통신오류!!</B></font></A>";
	}
}


$str_notice="";
$server_error=false;
$noticedata=array();
$resdata=getAdminMainNotice();
if(substr($resdata,0,2)=="OK") {
	$noticedata=unserialize(substr($resdata,3));
} else {
	$server_error=true;
}
for($i=0;$i<count($noticedata);$i++) {
	$row=$noticedata[$i];
	$row->subject=str_replace("'","`",$row->subject);
	$row->subject=str_replace("\"","`",$row->subject);
	$str_notice.="<tr>";
	$str_notice.="	<td width=8><img src=images/main_center_point.gif border=0></td>";
	$str_notice.="	<td><font color=gray>[".substr($row->date,4,2)."/".substr($row->date,6,2)."]</font> <A HREF=\"javascript:shop_noticeview(\'view\',\'{$row->date}\')\">".titleCut(23,$row->subject)."</A></td>";
	$str_notice.="</tr>";
}
if($i==0) {
	if($server_error) {
		$str_notice.="<tr><td align=center><font class=font_orange>공지사항 서버와 통신이 불가능합니다.</font></td></tr>";
	} else {
		$str_notice.="<tr><td align=center>등록된 공지사항이 없습니다.</td></tr>";
	}
}
$str_notice="<table cellpadding=0 cellspacing=0 width=100% border=0>{$str_notice}</table>";
?>
<script>
try {
	parent.document.all["idx_sms"].innerHTML='<?=$sms_count?>';
} catch(e) {}

try {
	parent.document.all["idx_notice"].innerHTML='<?=$str_notice?>';
} catch(e) {}
</script>