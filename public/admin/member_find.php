<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$formname=$_POST["formname"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>회원 아이디 검색</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}

	if(ekey==13) {
		SearchMember();
		return false;
	}
}

function PageResize() {
	var oWidth = 450;
	var oHeight = document.all.table_body.clientHeight + 120;
	//var oHeight = 300;

	window.resizeTo(oWidth,oHeight);
}

function SearchMember() {
	if(document.form1.search.value.length==0) {
		alert("회원 아이디, 또는 회원명을 입력하세요.");
		document.form1.search.focus();
		return;
	}
	if(document.form1.search.value.length<=2) {
		alert("검색 키워드는 2자 이상 입력하셔야 합니다.");
		document.form1.search.focus();
		return;
	}
	document.form1.submit();
}

function selectid(id) {
	opener.document[document.form1.formname.value].id.value = id;
	window.close();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>회원 아이디 검색</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>회원 아이디 검색</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=formname value="<?=$formname?>">
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="20">&nbsp;</td>
		<td width="392" height="30">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height=7></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="403" align=center valign="top" class="font_size">
			<select name="s_check" size="1" class="input">
			<option value="id" <?php if($s_check=="id") echo "checked";?>>아이디</option>
			<option value="name" <?php if($s_check=="name") echo "checked";?>>회원명</option>
			</select>
			<INPUT maxLength=20 name=search class="input" size="24" style="WIDTH:200;height:19" value="<?=$search?>">
			<a href="javascript:SearchMember();"><img src="images/btn_search.gif" width="62" height="21" border="0" align=absmiddle hspace="3"></a><br>
			&nbsp;*아이디,회원명으로 조회하실 수 있습니다.</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="20">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="20">&nbsp;</td>
		<td height="25">
		<?php if(ord($search) && ord($s_check)) {?>
		<table cellpadding="0" cellspacing="0" width="403">
		<tr>
			<td>
			<table cellpadding="5" cellspacing="0" width="100%" bgcolor="#F3F3F3">
			<tr>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;"><b>아이디</b></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;"><b>이름</b></p></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;"><b>전화번호</b></p></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;"><b>비고</b></p></td>
			</tr>
<?php
			$sql = "SELECT id, name, home_tel, resno FROM tblmember ";
			$sql.= "WHERE member_out = 'N' AND {$s_check} LIKE '%{$search}%' ";
			$result = pmysql_query($sql,get_db_conn());
			$count=0;
			while($row=pmysql_fetch_object($result)) {
				$count++;
				echo "<tr>\n";
				echo "	<td bgcolor=\"white\" align=\"center\"><A HREF=\"javascript:selectid('{$row->id}');\"><span class=\"font_blue\"><B><U>{$row->id}</U></B></span></A></td>\n";
				echo "	<td bgcolor=\"white\" align=\"center\">{$row->name}</td>\n";
				echo "	<td bgcolor=\"white\" align=center>{$row->home_tel}</td>\n";
				echo "	<td bgcolor=\"white\" align=center><a href=\"javascript:selectid('{$row->id}');\"><img src=\"images/btn_add.gif\" width=\"59\" height=\"25\" border=\"0\"></a></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td bgcolor=\"white\" align=\"center\" colspan=\"4\" background=\"images/table_con_line.gif\"></td>\n";
				echo "</tr>\n";
			}
			pmysql_free_result($result);
?>									
			</table>
			</td>
		</tr>
		</table>
		<?php }?>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="20">&nbsp;</td>
		<td width="392" align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="5" border=0></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</TD>
</TR>
</form>
</TABLE>
</body>
</html>
