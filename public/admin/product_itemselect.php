<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>브랜드 선택하기</title>
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
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function SearchSubmit(seachIdxval) {
	form = document.form1;

	form.seachIdx.value = seachIdxval;
	form.submit();
}

function Result() {
	try {
		if(document.form1.up_brandlist.selectedIndex>-1) {
			opener.document.form1.itemname.value=document.form1.up_brandlist.options[document.form1.up_brandlist.selectedIndex].text;
			window.close();
		} else {
			alert('적용할 브랜드를 선택해 주세요.');
		}
	} catch(e) {
		alert('상품등록/수정 페이지에서만 적용됩니다.');
	}
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>브랜드 선택하기</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>브랜드 선택하기</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 245;HEIGHT: 320;}
</STYLE>
<TABLE WIDTH="420" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD height="10"></TD>
	</TR>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=seachIdx value="">
	<tr>
		<TD style="padding-left:4pt;padding-right:4pt;" valign="top">
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr>
			<td style="padding:5px;padding-left:2px;padding-right:2px;"><b><a href="javascript:SearchSubmit('A');"><span id="A">A</span></a> 
			<a href="javascript:SearchSubmit('B');"><span id="B">B</span></a> 
			<a href="javascript:SearchSubmit('C');"><span id="C">C</span></a> 
			<a href="javascript:SearchSubmit('D');"><span id="D">D</span></a> 
			<a href="javascript:SearchSubmit('E');"><span id="E">E</span></a> 
			<a href="javascript:SearchSubmit('F');"><span id="F">F</span></a> 
			<a href="javascript:SearchSubmit('G');"><span id="G">G</span></a> 
			<a href="javascript:SearchSubmit('H');"><span id="H">H</span></a> 
			<a href="javascript:SearchSubmit('I');"><span id="I">I</span></a> 
			<a href="javascript:SearchSubmit('J');"><span id="J">J</span></a> 
			<a href="javascript:SearchSubmit('K');"><span id="K">K</span></a> 
			<a href="javascript:SearchSubmit('L');"><span id="L">L</span></a> 
			<a href="javascript:SearchSubmit('M');"><span id="M">M</span></a> 
			<a href="javascript:SearchSubmit('N');"><span id="N">N</span></a> 
			<a href="javascript:SearchSubmit('O');"><span id="O">O</span></a> 
			<a href="javascript:SearchSubmit('P');"><span id="P">P</span></a> 
			<a href="javascript:SearchSubmit('Q');"><span id="Q">Q</span></a> 
			<a href="javascript:SearchSubmit('R');"><span id="R">R</span></a> 
			<a href="javascript:SearchSubmit('S');"><span id="S">S</span></a> 
			<a href="javascript:SearchSubmit('T');"><span id="T">T</span></a> 
			<a href="javascript:SearchSubmit('U');"><span id="U">U</span></a> 
			<a href="javascript:SearchSubmit('V');"><span id="V">V</span></a> 
			<a href="javascript:SearchSubmit('W');"><span id="W">W</span></a> 
			<a href="javascript:SearchSubmit('X');"><span id="X">X</span></a> 
			<a href="javascript:SearchSubmit('Y');"><span id="Y">Y</span></a> 
			<a href="javascript:SearchSubmit('Z');"><span id="Z">Z</span></a></b></td>
			<td width="40" align="center" nowrap><b><a href="javascript:SearchSubmit('전체');"><span id="전체">전체</span></a></b></td>
		</tr>
		<tr>
			<!-- 상품카테고리 목록 -->
			<td rowspan="2"><select name="up_brandlist" size="20" style="width:100%;" ondblclick="Result();">
<?php
	$sql = "SELECT * FROM tblproductitem ";
	if(preg_match("/^[A-Z]/", $seachIdx)) {
		$sql.= "WHERE itemname LIKE '{$seachIdx}%' OR itemname LIKE '".strtolower($seachIdx)."%' ";	
		$sql.= "ORDER BY itemname ";
	} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
		if($seachIdx == "ㄱ") $sql.= "WHERE (itemname >= 'ㄱ' AND itemname < 'ㄴ') OR (itemname >= '가' AND itemname < '나') ";
		if($seachIdx == "ㄴ") $sql.= "WHERE (itemname >= 'ㄴ' AND itemname < 'ㄷ') OR (itemname >= '나' AND itemname < '다') ";
		if($seachIdx == "ㄷ") $sql.= "WHERE (itemname >= 'ㄷ' AND itemname < 'ㄹ') OR (itemname >= '다' AND itemname < '라') ";
		if($seachIdx == "ㄹ") $sql.= "WHERE (itemname >= 'ㄹ' AND itemname < 'ㅁ') OR (itemname >= '라' AND itemname < '마') ";
		if($seachIdx == "ㅁ") $sql.= "WHERE (itemname >= 'ㅁ' AND itemname < 'ㅂ') OR (itemname >= '마' AND itemname < '바') ";
		if($seachIdx == "ㅂ") $sql.= "WHERE (itemname >= 'ㅂ' AND itemname < 'ㅅ') OR (itemname >= '바' AND itemname < '사') ";
		if($seachIdx == "ㅅ") $sql.= "WHERE (itemname >= 'ㅅ' AND itemname < 'ㅇ') OR (itemname >= '사' AND itemname < '아') ";
		if($seachIdx == "ㅇ") $sql.= "WHERE (itemname >= 'ㅇ' AND itemname < 'ㅈ') OR (itemname >= '아' AND itemname < '자') ";
		if($seachIdx == "ㅈ") $sql.= "WHERE (itemname >= 'ㅈ' AND itemname < 'ㅊ') OR (itemname >= '자' AND itemname < '차') ";
		if($seachIdx == "ㅊ") $sql.= "WHERE (itemname >= 'ㅊ' AND itemname < 'ㅋ') OR (itemname >= '차' AND itemname < '카') ";
		if($seachIdx == "ㅋ") $sql.= "WHERE (itemname >= 'ㅋ' AND itemname < 'ㅌ') OR (itemname >= '카' AND itemname < '타') ";
		if($seachIdx == "ㅌ") $sql.= "WHERE (itemname >= 'ㅌ' AND itemname < 'ㅍ') OR (itemname >= '타' AND itemname < '파') ";
		if($seachIdx == "ㅍ") $sql.= "WHERE (itemname >= 'ㅍ' AND itemname < 'ㅎ') OR (itemname >= '파' AND itemname < '하') ";
		if($seachIdx == "ㅎ") $sql.= "WHERE (itemname >= 'ㅎ' AND itemname < 'ㅏ') OR (itemname >= '하' AND itemname < '') ";
		$sql.= "ORDER BY itemname ";
	} else if($seachIdx == "기타") {
		$sql.= "WHERE (itemname < 'ㄱ' OR itemname >= 'ㅏ') AND (itemname < '가' OR itemname >= '') AND (itemname < 'a' OR itemname >= '{') AND (itemname < 'A' OR itemname >= '[') ";
		$sql.= "ORDER BY itemname ";
	} else {
		$sql.= "ORDER BY itemname ";
	}

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		echo "<option value=\"{$row->itidx}\">{$row->itemname}</option>";
	}
?>
			</select></td>
			<td width="40" align="center" nowrap style="line-height:21px;" valign="top"><b><a href="javascript:SearchSubmit('ㄱ');"><span id="ㄱ">ㄱ</span></a><br>
			<a href="javascript:SearchSubmit('ㄴ');"><span id="ㄴ">ㄴ</span></a><br>
			<a href="javascript:SearchSubmit('ㄷ');"><span id="ㄷ">ㄷ</span></a><br>
			<a href="javascript:SearchSubmit('ㄹ');"><span id="ㄹ">ㄹ</span></a><br>
			<a href="javascript:SearchSubmit('ㅁ');"><span id="ㅁ">ㅁ</span></a><br>
			<a href="javascript:SearchSubmit('ㅂ');"><span id="ㅂ">ㅂ</span></a><br>
			<a href="javascript:SearchSubmit('ㅅ');"><span id="ㅅ">ㅅ</span></a><br>
			<a href="javascript:SearchSubmit('ㅇ');"><span id="ㅇ">ㅇ</span></a><br>
			<a href="javascript:SearchSubmit('ㅈ');"><span id="ㅈ">ㅈ</span></a><br>
			<a href="javascript:SearchSubmit('ㅊ');"><span id="ㅊ">ㅊ</span></a><br>
			<a href="javascript:SearchSubmit('ㅋ');"><span id="ㅋ">ㅋ</span></a><br>
			<a href="javascript:SearchSubmit('ㅌ');"><span id="ㅌ">ㅌ</span></a><br>
			<a href="javascript:SearchSubmit('ㅍ');"><span id="ㅍ">ㅍ</span></a><br>
			<a href="javascript:SearchSubmit('ㅎ');"><span id="ㅎ">ㅎ</span></a><br>
			<a href="javascript:SearchSubmit('기타');"><span id="기타">기타</span></a></b></td>
			<!-- 상품카테고리 목록 끝 -->
		</tr>
		</table>
		</TD>
	</tr>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD align=center><a href="javascript:Result();"><img src="images/btn_select1.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" hspace="2"></a></TD>
	</TR>
	</form>
	</table>
	</td>
</tr>
</TABLE>
<script language="javascript">
<!--
<?php
	if(ord($seachIdx)) {
		echo "document.getElementById(\"$seachIdx\").style.color=\"#FF4C00\";";
	} else {
		echo "document.getElementById(\"전체\").style.color=\"#FF4C00\";";
	}
?>
//-->
</script>
</body>
</html>
