<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$type = $_REQUEST["type"];

$mode = $_POST["mode"];
$seachIdx = $_POST["seachIdx"];
$selectname = $_POST["selectname"];
$up_selectlist = $_POST["up_selectlist"];

if($type!="PR" && $type!="MA" && $type!="MO") {
	echo "<script>alert('처리를 위한 데이타가 부족합니다.');window.close();</script>";
	exit;
} else {
	$fieldname = array("PR"=>"production", "MA"=>"madein", "MO"=>"model");
	$pagename = array("PR"=>"제조사를", "MA"=>"원산지를", "MO"=>"매입처를");
	$valuemax = array("PR"=>"50", "MA"=>"30", "MO"=>"50");
}

if($mode == "insert" && ord($selectname)) {
	$sql = "INSERT INTO tblproductselect(type,selectname) VALUES (
	'{$type}', 
	'{$selectname}')";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('등록이 정상적으로 완료 됐습니다.');</script>";
} else if($mode == "delete" && ord($up_selectlist)) {
	$sql = "DELETE FROM tblproductselect ";
	$sql.= "WHERE type='{$type}' AND num = '{$up_selectlist}' ";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('삭제가 정상적으로 완료 됐습니다.');</script>";
}


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$pagename[$type]?> 선택하기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
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
	form.mode.value = "search";
	form.submit();
}

function Result() {
	try {
		if(opener.document.form1.<?=$fieldname[$type]?>) {
			if(document.form1.up_selectlist.selectedIndex>-1) {
				opener.document.form1.<?=$fieldname[$type]?>.value=document.form1.up_selectlist.options[document.form1.up_selectlist.selectedIndex].text;
				window.close();
			} else {
				alert('적용할 <?=$pagename[$type]?> 선택해 주세요.');
			}
		} else {
			alert('적용할 폼이 존재하지 않습니다.');
		}
	} catch(e) {
		alert('상품등록/수정 페이지에서만 적용됩니다.');
	}
}

function CheckForm(modeval) {
	form = document.form1;
	
	if(modeval=="insert" && !form.selectname.value) {
		alert('등록할 <?=$pagename[$type]?> 입력해 주세요.');
	} else if(modeval=="delete" && document.form1.up_selectlist.selectedIndex==-1) {
		alert('삭제할 <?=$pagename[$type]?> 선택해 주세요.');
	} else {
		if(modeval=="insert" && confirm("<?=$pagename[$type]?> 정말 등록하겠습니까?")) {
			form.mode.value = modeval;
			form.submit();
		} else if(modeval=="delete" && confirm("삭제를 하더라도 기존 입력된 상품 정보는 삭제 되지 않습니다.\n\n<?=$pagename[$type]?> <?=$pagename[$type]?> 정말 삭제하겠습니까?")) {
			form.mode.value = modeval;
			form.submit();
		}
	}
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$pagename[$type]?> 선택하기</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$pagename[$type]?> 선택하기</p></div>
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
	<input type=hidden name=mode value="">
	<input type=hidden name=type value="<?=$type?>">
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
			<td><select name="up_selectlist" size="20" style="width:100%;" ondblclick="Result();">
<?php
	$sql = "SELECT * FROM tblproductselect ";
	$sql .= "WHERE 1=1";
	//$sql .= "AND type='{$type}' ";

	if(preg_match("/^[A-Z]/", $seachIdx)) {
		$sql.= "AND selectname LIKE '{$seachIdx}%' OR selectname LIKE '".strtolower($seachIdx)."%' ";	
		$sql .= "AND type='{$type}' ";
		$sql.= "ORDER BY selectname ";
	} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
		if($seachIdx == "ㄱ") $sql.= "AND (selectname >= 'ㄱ' AND selectname < 'ㄴ') OR (selectname >= '가' AND selectname < '나') ";
		if($seachIdx == "ㄴ") $sql.= "AND (selectname >= 'ㄴ' AND selectname < 'ㄷ') OR (selectname >= '나' AND selectname < '다') ";
		if($seachIdx == "ㄷ") $sql.= "AND (selectname >= 'ㄷ' AND selectname < 'ㄹ') OR (selectname >= '다' AND selectname < '라') ";
		if($seachIdx == "ㄹ") $sql.= "AND (selectname >= 'ㄹ' AND selectname < 'ㅁ') OR (selectname >= '라' AND selectname < '마') ";
		if($seachIdx == "ㅁ") $sql.= "AND (selectname >= 'ㅁ' AND selectname < 'ㅂ') OR (selectname >= '마' AND selectname < '바') ";
		if($seachIdx == "ㅂ") $sql.= "AND (selectname >= 'ㅂ' AND selectname < 'ㅅ') OR (selectname >= '바' AND selectname < '사') ";
		if($seachIdx == "ㅅ") $sql.= "AND (selectname >= 'ㅅ' AND selectname < 'ㅇ') OR (selectname >= '사' AND selectname < '아') ";
		if($seachIdx == "ㅇ") $sql.= "AND (selectname >= 'ㅇ' AND selectname < 'ㅈ') OR (selectname >= '아' AND selectname < '자') ";
		if($seachIdx == "ㅈ") $sql.= "AND (selectname >= 'ㅈ' AND selectname < 'ㅊ') OR (selectname >= '자' AND selectname < '차') ";
		if($seachIdx == "ㅊ") $sql.= "AND (selectname >= 'ㅊ' AND selectname < 'ㅋ') OR (selectname >= '차' AND selectname < '카') ";
		if($seachIdx == "ㅋ") $sql.= "AND (selectname >= 'ㅋ' AND selectname < 'ㅌ') OR (selectname >= '카' AND selectname < '타') ";
		if($seachIdx == "ㅌ") $sql.= "AND (selectname >= 'ㅌ' AND selectname < 'ㅍ') OR (selectname >= '타' AND selectname < '파') ";
		if($seachIdx == "ㅍ") $sql.= "AND (selectname >= 'ㅍ' AND selectname < 'ㅎ') OR (selectname >= '파' AND selectname < '하') ";
		if($seachIdx == "ㅎ") $sql.= "AND (selectname >= 'ㅎ' AND selectname < 'ㅏ') OR (selectname >= '하' AND selectname < '') ";
		$sql .= "AND type='{$type}' ";
		$sql.= "ORDER BY selectname ";
	} else if($seachIdx == "기타") {
		$sql.= "AND (selectname < 'ㄱ' OR selectname >= 'ㅏ') AND (selectname < '가' OR selectname >= '') AND (selectname < 'a' OR selectname >= '{') AND (selectname < 'A' OR selectname >= '[') ";
		$sql .= "AND type='{$type}' ";
		$sql.= "ORDER BY selectname ";
	} else {
		$sql.= "ORDER BY selectname ";
	}

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		echo "<option value=\"{$row->num}\">{$row->selectname}</option>";
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
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td><?=$pagename[$type]?> 등록 : <input type="text" name="selectname" value="" class="input" size="38" onKeyDown="chkFieldMaxLen(<?=$valuemax[$type]?>)"> <a href="javascript:CheckForm('insert');"><img src="images/btn_input.gif" border="0" align="absmiddle"></a></td>
			<td align="center"><a href="javascript:CheckForm('delete');"><img src="images/btn_delete1.gif" border="0" align="absmiddle"></a></td>
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
<?=$onload?>
</body>
</html>
