<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$cno = $_REQUEST["cno"];
$seachIdx = $_POST["seachIdx"];
$mode = $_POST["mode"];
$name = $_POST["name"];
$up_selectlist = $_POST["up_selectlist"];
$cate_no = $_POST["cate_no"];

if($mode == "insert" && ord($name)) {
	$sql = "INSERT INTO tblimgcollectioncate(cno,name) VALUES (
	'{$cno}', 
	'{$name}')";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('등록이 정상적으로 완료 됐습니다.');opener.location.reload();</script>";
} else if($mode == "delete" && ord($up_selectlist)) {
	$sql = "DELETE FROM tblimgcollectioncate ";
	$sql.= "WHERE cno='{$cno}' AND no = '{$up_selectlist}' ";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('삭제가 정상적으로 완료 됐습니다.');opener.location.reload();</script>";
} else if($mode == "modify" && ord($cate_no) && ord($name)) {
	$sql = "UPDATE tblimgcollectioncate SET ";
	$sql .= "name='{$name}' ";
	$sql.= "WHERE cno='{$cno}' AND no = '{$cate_no}' ";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('수정이 정상적으로 완료 됐습니다.');opener.location.reload();</script>";
} else if($mode == "modifySel") {	
	$bSelectSql = "SELECT * FROM tblimgcollectioncate WHERE no ='{$up_selectlist}' ";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_array( $bSelectRes );
	$mSelect = $bSelectRow;
	pmysql_free_result( $bSelectRes );
}


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>카테고리 관리</title>
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

function CheckForm(modeval) {
	form = document.form1;
	
	if(modeval=="insert" && !form.name.value) {
		alert('등록할 카테고리를 입력해 주세요.');
	} else if(modeval=="modify" && !form.name.value) {
		alert('수정할 카테고리를 입력해 주세요.');
	} else if(modeval=="modifySel" && document.form1.up_selectlist.selectedIndex==-1) {
		alert('수정할 카테고리를 선택해 주세요.');
	} else if(modeval=="delete" && document.form1.up_selectlist.selectedIndex==-1) {
		alert('삭제할 카테고리를 선택해 주세요.');
	} else {
		if(modeval=="insert" && confirm("카테고리를 정말 등록하겠습니까?")) {
			form.mode.value = modeval;
			form.submit();
		} else if(modeval=="modifySel") {
			form.mode.value = modeval;
			form.submit();
		} else if(modeval=="modify" && confirm("카테고리를 정말 수정하겠습니까?")) {
			form.mode.value = modeval;
			form.submit();
		} else if(modeval=="delete" && confirm("삭제를 하더라도 기존 입력된 사진 정보는 삭제 되지 않습니다.\n\n카테고리를 정말 삭제하겠습니까?")) {
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
<title>카테고리 관리</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>카테고리 관리</p></div>
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
	<input type=hidden name=cno value="<?=$cno?>">
	<input type=hidden name=seachIdx value="">
	<input type=hidden name=cate_no value="<?=$mSelect['no']?>">
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
			<td><select name="up_selectlist" size="20" style="width:100%;">
<?php
	$sql = "SELECT * FROM tblimgcollectioncate ";
	$sql .= "WHERE 1=1";

	if(preg_match("/^[A-Z]/", $seachIdx)) {
		$sql.= "AND name LIKE '{$seachIdx}%' OR name LIKE '".strtolower($seachIdx)."%' ";	
		$sql .= "AND cno='{$cno}' ";
		$sql.= "ORDER BY no ";
	} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
		if($seachIdx == "ㄱ") $sql.= "AND (name >= 'ㄱ' AND name < 'ㄴ') OR (name >= '가' AND name < '나') ";
		if($seachIdx == "ㄴ") $sql.= "AND (name >= 'ㄴ' AND name < 'ㄷ') OR (name >= '나' AND name < '다') ";
		if($seachIdx == "ㄷ") $sql.= "AND (name >= 'ㄷ' AND name < 'ㄹ') OR (name >= '다' AND name < '라') ";
		if($seachIdx == "ㄹ") $sql.= "AND (name >= 'ㄹ' AND name < 'ㅁ') OR (name >= '라' AND name < '마') ";
		if($seachIdx == "ㅁ") $sql.= "AND (name >= 'ㅁ' AND name < 'ㅂ') OR (name >= '마' AND name < '바') ";
		if($seachIdx == "ㅂ") $sql.= "AND (name >= 'ㅂ' AND name < 'ㅅ') OR (name >= '바' AND name < '사') ";
		if($seachIdx == "ㅅ") $sql.= "AND (name >= 'ㅅ' AND name < 'ㅇ') OR (name >= '사' AND name < '아') ";
		if($seachIdx == "ㅇ") $sql.= "AND (name >= 'ㅇ' AND name < 'ㅈ') OR (name >= '아' AND name < '자') ";
		if($seachIdx == "ㅈ") $sql.= "AND (name >= 'ㅈ' AND name < 'ㅊ') OR (name >= '자' AND name < '차') ";
		if($seachIdx == "ㅊ") $sql.= "AND (name >= 'ㅊ' AND name < 'ㅋ') OR (name >= '차' AND name < '카') ";
		if($seachIdx == "ㅋ") $sql.= "AND (name >= 'ㅋ' AND name < 'ㅌ') OR (name >= '카' AND name < '타') ";
		if($seachIdx == "ㅌ") $sql.= "AND (name >= 'ㅌ' AND name < 'ㅍ') OR (name >= '타' AND name < '파') ";
		if($seachIdx == "ㅍ") $sql.= "AND (name >= 'ㅍ' AND name < 'ㅎ') OR (name >= '파' AND name < '하') ";
		if($seachIdx == "ㅎ") $sql.= "AND (name >= 'ㅎ' AND name < 'ㅏ') OR (name >= '하' AND name < '') ";
		$sql .= "AND cno='{$cno}' ";
		$sql.= "ORDER BY no ";
	} else if($seachIdx == "기타") {
		$sql.= "AND (name < 'ㄱ' OR name >= 'ㅏ') AND (name < '가' OR name >= '') AND (name < 'a' OR name >= '{') AND (name < 'A' OR name >= '[') ";
		$sql .= "AND cno='{$cno}' ";
		$sql.= "ORDER BY no ";
	} else {
		$sql.= "ORDER BY no ";
	}

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		echo "<option value=\"{$row->no}\">{$row->name}</option>";
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
			<td>카테고리 등록 : <input type="text" name="name" value="<?=$mSelect['name']?>" class="input" size="28"> <a href="javascript:CheckForm('<?if($mode=="modifySel") {echo "modify"; } else { echo "insert";}?>');"><img src="images/btn_input.gif" border="0" align="absmiddle"></a></td>
			<td align="center"><a href="javascript:CheckForm('modifySel');"><img src="img/btn/btn_cate_modify.gif" border="0" align="absmiddle"></a><BR><a href="javascript:CheckForm('delete');"><img src="img/btn/btn_cate_del01.gif" border="0" align="absmiddle"></a></td>
		</tr>
		</table>
		</TD>
	</tr>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD align=center><a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" hspace="2"></a></TD>
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
