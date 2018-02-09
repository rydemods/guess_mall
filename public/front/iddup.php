<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id=$_REQUEST["id"];

if(strlen($id)<4 || strlen($id)>12) {
	$message="<font color=#FF3300><b>아이디는 4~12자 까지 입력 가능합니다.</b></font>";
} else if(!IsAlphaNumeric($id)) {
	$message="<font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font>";
} else if(!preg_match("/(^[0-9a-zA-Z]{4,12}$)/",$id)) {
	$message="<font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font>";
} else if(preg_match("/(\'|\"|\,|\.|&|%|<|>|\/|\||\\\\|[ ])/",$id)) {
    $message="<font color=#FF3300><b>사용 불가능한 문자가 사용되었습니다.</b></font>";
} else if(strlen($id)<=0) {
    $message="<font color=#FF3300><b>아이디 입력이 안되었습니다.</b></font>";
} else if(strtolower($id)=="admin") {
    $message="<font color=#FF3300><b>사용 불가능한 아이디 입니다.</b></font>";
} else {
	$sql = "SELECT id FROM tblmember WHERE id='{$id}' ";
	$result = pmysql_query($sql,get_db_conn());

	if ($row=pmysql_fetch_object($result)) {
		$message="<font color=#ff0000><b>아이디가 중복되었습니다.</b></font>";
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='{$id}' ";
		$result2 = pmysql_query($sql,get_db_conn());
		if($row2=pmysql_fetch_object($result2)) {
			$message="<font color=#ff0000><b>아이디가 중복되었습니다.</b></font>";
		} else {
			$message="<font color=#0000ff><b>사용가능한 아이디 입니다.</b></font>";
		}
		pmysql_free_result($result2);
	}
	pmysql_free_result($result);
}

$body='';
$sql="SELECT body FROM tbldesignnewpage WHERE type='iddup'";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
}
pmysql_free_result($result);
?>
<html>
<head>
<title>아이디 중복 확인</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" onload="window.resizeTo(276,200);">
<?php 
if(ord($body)) {
	$pattern=array("[MESSAGE]","[OK]");
	$replace=array($message,"JavaScript:window.close()");
	$body = str_replace($pattern,$replace,$body);
	if (strpos(strtolower($body),"table")!=false) $body = "<pre>{$body}</pre>";
	else $body = nl2br($body);

	echo $body;
} else {
?>
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD><img src="<?=$Dir?>images/design_adultintro_ids_t.gif" border="0"></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="50" align="center"><?=$message?></td>
	</tr>
	<tr>
		<td><hr size="1" noshade color="#F3F3F3"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:window.close()"><img src="<?=$Dir?>images/btn_ok4.gif" border="0"></a></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
<?php }?>
</center>
</body>
</html>
