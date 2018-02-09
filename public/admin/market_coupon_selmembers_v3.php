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
	var issue_selmembers = opener.document[document.form1.formname.value].issue_selmembers.value;
	var mem_arr	= issue_selmembers.split(',');
	if (mem_arr.indexOf(id) != -1) {
		alert('이미 추가된 회원입니다.');
	} else {
		if (issue_selmembers !='') id = ',' + id;
		opener.document[document.form1.formname.value].issue_selmembers.value = issue_selmembers + id;
		opener.document.getElementById("ID_membersLayer").innerHTML = " <img src='img/icon/table_bull.gif'> " + issue_selmembers.replace(/,/gi, "<br> <img src='img/icon/table_bull.gif'> ") + id.replace(/,/gi, "<br> <img src='img/icon/table_bull.gif'> ");
		//alert(opener.document[document.form1.formname.value].issue_selmembers.value);
	}
	//window.close();
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
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">아이디,회원명으로 조회하실 수 있습니다.</td>
	</tr>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=formname value="<?=$formname?>">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td width="42">
				<select name="s_check" size="1" class="input_selected" style='padding-top:3px;padding-bottom:1px;height:22px'>
				<option value="id" <?php if($s_check=="id") echo "checked";?>>아이디</option>
				<option value="name" <?php if($s_check=="name") echo "checked";?>>회원명</option>
				</select>
			</td>
			<td><INPUT maxLength=20 name=search class="input_selected" size="24" style="WIDTH:300px;height:22px" value="<?=$search?>"></td>
			<td width="40" align=right><a href="javascript:SearchMember();"><img src="images/btn_search2.gif" border="0" valign=top></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	<?php if(ord($search) && ord($s_check)) {?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:5pt;"><b><font color="black">회원내역</b>(회원내역을 확인하실수 있습니다.)</font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<tr>
			<th>아이디</th>
			<th>이름</th>
			<th>전화번호</th>
			<th>비고</th>
		</tr>
<?php
		$sql = "SELECT id, name, home_tel, resno FROM tblmember ";
		$sql.= "WHERE member_out = 'N' AND {$s_check} LIKE '%{$search}%' ";
		$result = pmysql_query($sql,get_db_conn());
		$count=0;
		while($row=pmysql_fetch_object($result)) {
			$count++;
			echo "<tr>\n";
			echo "	<td width=130 style='text-align:left;padding-left:5px'><span class=\"font_blue\"><B>{$row->id}</B></span></td>\n";
			echo "	<td style='text-align:left;padding-left:5px'>{$row->name}</td>\n";
			echo "	<td width=90 style='text-align:left;padding-left:5px'>{$row->home_tel}</td>\n";
			echo "	<td width=60><a href=\"javascript:selectid('{$row->id}');\"><img src=\"images/btn_add.gif\" width=\"59\" height=\"25\" border=\"0\"></a></td>\n";
			echo "</tr>\n";
		}
		pmysql_free_result($result);
?>									
		</table>
        </div>
		</td>
	</tr>
	<?php }?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
