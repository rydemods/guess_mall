<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$form=$_REQUEST["form"];
$post=$_REQUEST["post"];
$addr=$_REQUEST["addr"];
$gbn=$_REQUEST["gbn"];

$area=trim($_POST["area"]);
$mode=$_POST["mode"];

if (strlen($area)>2 && (strpos($_SERVER['HTTP_REFERER'],"popup_zipcode.php")===false || strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false)) {
	exit;
}
?>
<html>
<head>
<title>�����ȣ �˻�</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<style>
td	{font-family:"����,����";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:����;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function EnterCheck() {
	if (document.form.area.value.length==0) {
		alert("��(��/��/��) �̸��� �Է��ϼ���.");
		document.form.area.focus();
		return;
	} else {
		if (document.form.area.value.length<2) {
			alert("��(��/��/��) �̸��� 2�� �̻� �Է��ϼ���.");
			document.form.area.focus();
			return;
		}
		document.form.submit();
	}
}

var form="<?=$form?>";
var post="<?=$post?>";
var addr="<?=$addr?>";
var gbn="<?=$gbn?>";
function do_submit(post1,post2,straddr) {
	try {
		if(gbn=="2") {
			parent.document[form][post+"1"].value=post1;
			parent.document[form][post+"2"].value=post2;
		} else {
			parent.document[form][post].value=post1+"-"+post2;
		}
		parent.document[form][addr].value=straddr;
		parent.document[form][addr].focus();
		parent.postclose();
	} catch (e) {
		alert("������ �߻��Ͽ����ϴ�.");
	}
}
//-->
</SCRIPT>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" onLoad="window.resizeTo(460,300);document.form.area.focus();">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<TD></TD>
</TR>
<TR>
	<TD style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" height="30">
		<table cellpadding="0" cellspacing="0" width="100%">
		<form method="POST" name="form" action="<?= $_SERVER['PHP_SELF'] ?>?form=<?=$form?>&post=<?=$post?>&addr=<?=$addr?>&gbn=<?=$gbn?>">
		<input type=hidden name=mode value="srch">
		<tr>
			<td colspan="2" height="10"></td>
		</tr>
		<tr>
			<td colspan="2"><img src="<?=$Dir?>images/search_zipcode_text1.gif" border="0"></td>
		</tr>
		<tr>
			<td colspan="2" height="10"></td>
		</tr>
		<tr>
			<td width="100%" valign="top"><input type=text name=area value="<?=$area?>" size=20 class="input" style="WIDTH:100%;"></td>
			<td><a href="javascript:EnterCheck();"><img src="<?=$Dir?>images/search_zipcode_btn.gif" border="0" hspace="5"></a></td>
		</tr>
		<tr>
			<td colspan="2" height="5"></td>
		</tr>
		</form>
		</table>
		</td>
	</tr>
	<tr>
		<td><hr size="1" color="#F3F3F3"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
<?php
		if($mode=="srch" && strlen($area)>0) {
			$sql = "SELECT * FROM tblpostalcode ";
			$sql.= "WHERE addr_dong LIKE '%{$area}%' ";
			$result=pmysql_query($sql,get_db_conn());
			echo "<tr>\n";
			echo "	<td>\n";
			echo "	<table border=0 cellpadding=5 cellspacing=0 width=100%>\n";
			echo "	<col width=70></col><col width=></col>\n";
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				if ($i % 2 == 0) $trbg = "#F3F3F3";
				else $trbg = "#FFFFFF";
				$temp = substr($row->post,0,3)."-".substr($row->post,3,3);
				$temp2 = $row->addr_do." {$row->addr_si} {$row->addr_dong} ".$row->addr_bunji;
				$temp3 = $row->addr_do." {$row->addr_si} ".$row->addr_dong;
				echo "<tr bgcolor=\"$trbg\">\n";
				echo "	<td align=\"center\"><A HREF=\"javascript:do_submit('".substr($row->post,0,3)."','".substr($row->post,3,3)."','{$temp3}');\"><img src=\"{$Dir}images/search_zipcode_point3.gif\" border=\"0\"><font color=\"#FF6C00\"><b>{$temp}</b></font></A></td>\n";
				echo "	<td><A HREF=\"javascript:do_submit('".substr($row->post,0,3)."','".substr($row->post,3,3)."','{$temp3}');\" style=\"text-decoration:underline;\">{$temp2}</a></td>\n";
				echo "</tr>\n";
				$i++;
			}
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			pmysql_free_result($result);

			if($i==0) {
				echo "<tr>\n";
				echo "	<td align=center style=\"padding-top:10;color:#EE4900\"><B>�˻��� ����� �����ϴ�.</B></td>\n";
				echo "</tr>\n";
			} else {
				echo "<tr>\n";
				echo "	<td align=center style=\"padding-top:10;color:#EE4900\"><B>�ش� �ּҸ� ���� �� ������ �ּҸ� �Է��ϼ���.</B></td>\n";
				echo "</tr>\n";
			}
		} else {
			echo "<tr>\n";
			echo "	<td align=center style=\"padding-top:10;color:#EE4900\"><B>�ؿ� �ּ��� ��� �Է¶��� \"�ؿ�\"�� �Է��ϼ���.</B></td>\n";
			echo "</tr>\n";
		}
?>
		</table>
		</td>
	</tr>
	<tr>
		<td><hr size="1" color="#F3F3F3"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:parent.postclose();"><img src="<?=$Dir?>images/search_zipcode_btn_close.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>
