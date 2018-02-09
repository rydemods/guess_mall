<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$seachIdx = $_POST["seachIdx"];
$mode = $_POST['mode'];
$brandname = $_POST['brandname'];

if($mode == "insert" ) {
	$sql = "INSERT INTO tblproductbrand ( brandname ) VALUES ( '".$brandname."' )";
	@pmysql_query($sql,get_db_conn());
	$onload="<script>alert('등록이 정상적으로 완료 됐습니다.');</script>";
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
			opener.document.form1.brandname.value=document.form1.up_brandlist.options[document.form1.up_brandlist.selectedIndex].text;
			window.close();
		} else {
			alert('적용할 브랜드를 선택해 주세요.');
		}
	} catch(e) {
		alert('상품등록/수정 페이지에서만 적용됩니다.');
	}
}

function CheckForm(modeval) {
	form = document.form1;
	
	if(modeval=="insert" && !form.brandname.value) {
		alert('등록할 브랜드명을 입력해 주세요.');
	} else if(modeval=="delete" && document.form1.up_selectlist.selectedIndex==-1) {
		alert('삭제할 브랜드를 선택해 주세요.');
	} else {
		if(modeval=="insert" && confirm("브랜드를 정말 등록하겠습니까?")) {
			form.mode.value = modeval;
			form.submit();
		} else if(modeval=="delete" && confirm("삭제를 하더라도 기존 입력된 상품 정보는 삭제 되지 않습니다.\n\n 정말 삭제하겠습니까?")) {
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
	<input type='hidden' name='mode' value='' >
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
	$sql = "SELECT * FROM tblproductbrand ";
	$sql.= "WHERE 1=1 ";
	if(preg_match("/^[A-Z]/", $seachIdx)) {
		$sql.= "AND brandname LIKE '{$seachIdx}%' OR brandname LIKE '".strtolower($seachIdx)."%' ";	
		$sql.= "ORDER BY brandname ";
	} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
		if($seachIdx == "ㄱ") $sql.= "AND (brandname >= 'ㄱ' AND brandname < 'ㄴ') OR (brandname >= '가' AND brandname < '나') ";
		if($seachIdx == "ㄴ") $sql.= "AND (brandname >= 'ㄴ' AND brandname < 'ㄷ') OR (brandname >= '나' AND brandname < '다') ";
		if($seachIdx == "ㄷ") $sql.= "AND (brandname >= 'ㄷ' AND brandname < 'ㄹ') OR (brandname >= '다' AND brandname < '라') ";
		if($seachIdx == "ㄹ") $sql.= "AND (brandname >= 'ㄹ' AND brandname < 'ㅁ') OR (brandname >= '라' AND brandname < '마') ";
		if($seachIdx == "ㅁ") $sql.= "AND (brandname >= 'ㅁ' AND brandname < 'ㅂ') OR (brandname >= '마' AND brandname < '바') ";
		if($seachIdx == "ㅂ") $sql.= "AND (brandname >= 'ㅂ' AND brandname < 'ㅅ') OR (brandname >= '바' AND brandname < '사') ";
		if($seachIdx == "ㅅ") $sql.= "AND (brandname >= 'ㅅ' AND brandname < 'ㅇ') OR (brandname >= '사' AND brandname < '아') ";
		if($seachIdx == "ㅇ") $sql.= "AND (brandname >= 'ㅇ' AND brandname < 'ㅈ') OR (brandname >= '아' AND brandname < '자') ";
		if($seachIdx == "ㅈ") $sql.= "AND (brandname >= 'ㅈ' AND brandname < 'ㅊ') OR (brandname >= '자' AND brandname < '차') ";
		if($seachIdx == "ㅊ") $sql.= "AND (brandname >= 'ㅊ' AND brandname < 'ㅋ') OR (brandname >= '차' AND brandname < '카') ";
		if($seachIdx == "ㅋ") $sql.= "AND (brandname >= 'ㅋ' AND brandname < 'ㅌ') OR (brandname >= '카' AND brandname < '타') ";
		if($seachIdx == "ㅌ") $sql.= "AND (brandname >= 'ㅌ' AND brandname < 'ㅍ') OR (brandname >= '타' AND brandname < '파') ";
		if($seachIdx == "ㅍ") $sql.= "AND (brandname >= 'ㅍ' AND brandname < 'ㅎ') OR (brandname >= '파' AND brandname < '하') ";
		if($seachIdx == "ㅎ") $sql.= "AND (brandname >= 'ㅎ' AND brandname < 'ㅏ') OR (brandname >= '하' AND brandname < '') ";
		$sql.= "ORDER BY brandname ";
	} else if($seachIdx == "기타") {
		$sql.= "AND (brandname < 'ㄱ' OR brandname >= 'ㅏ') AND (brandname < '가' OR brandname >= '') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') ";
		$sql.= "ORDER BY brandname ";
	} else {
		$sql.= "ORDER BY brandname ";
	}

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		echo "<option value=\"{$row->bridx}\">{$row->brandname}</option>";
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
	<tr>
		<td>
			브랜드 등록 : 
			<input type="text" name="brandname" value="" class="input" size="38" >
			<a href="javascript:CheckForm('insert');">
				<img src="images/btn_input.gif" border="0" align="absmiddle">
			</a>
			<!-- <a href="javascript:CheckForm('delete');">
				<img src="images/btn_delete1.gif" border="0" align="absmiddle">
			</a> -->
		</td>
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
