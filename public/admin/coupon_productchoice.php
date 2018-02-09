<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$codetype=$_POST["codetype"];
$code=$_POST["code"];
if(strlen($code)!=12) $code="000000000000";

list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');

if(ord($codetype)==0) $codetype="ALL";

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 적용 상품군 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var code="<?=$code?>";
var cnt=0;
var codetypeTemp = "";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		selcode="ALL";
		seltype="";		
		document.all["code_top"].style.background="#dddddd";
		document.form1.codetype[0].checked=true;

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		document.form1.codetype[1].checked=true;
		if(cnt>0) {
			if(seltype.indexOf("X")!=-1) {
				document.form1.code.value=selcode;
				document.form1.submit();
			}
		}
		BodyInit(_code);
	}
	cnt++;
}

function ChangeProduct() {
	document.form1.codetype[2].checked=true;
}

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
	var oWidth = 400;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	codetype="";
	for(i=0;i<form.codetype.length;i++) {
		if(form.codetype[i].checked) {
			codetype=form.codetype[i].value;
			break;
		}
	}
	if(codetypeTemp != codetype){
		var tempHtml = "";
	}else{
		var tempHtml = $("#ID_productLayer", opener.document).html();
	}
	if(codetype=="ALL") {
		opener.document.form1.productcode.value="ALL";
		opener.document.form1.productname.value="전체상품";
		opener.ViewLayer('layer1','none');
	} else if (codetype=="CODE") {
		if(selcode.length!=12 || selcode=="000000000000"){
			alert('쿠폰 적용을 원하시는 카테고리를 선택하세요');
			return;
		}
		opener.document.form1.productcode.value=selcode;
		opener.document.form1.productname.value=selcode_name;
		opener.ViewLayer('layer1','block');

		$("#ID_productLayer", opener.document).html(tempHtml+"<div>"+selcode_name+"</div>");
	} else if (codetype=="PRODUCT") {
		if(form.prcode.value.length==0){
			alert('쿠폰 적용을 원하시는 상품을 선택하세요');
			form.prcode.focus();
			return;
		}
		opener.document.form1.productcode.value=form.prcode.value;
		opener.document.form1.productname.value=selcode_name+" > "+form.prcode.options[form.prcode.selectedIndex].text;
		opener.ViewLayer('layer1','block');

		$("#ID_productLayer", opener.document).html(tempHtml+"<div>"+selcode_name+" > "+form.prcode.options[form.prcode.selectedIndex].text+"</div>");
	} else {
		alert("쿠폰 적용 상품군 선택이 안되었습니다.");
		return;
	}
	//window.close();
}
$(document).ready(function(){
	$("#idx_codetype2").trigger("click");
})
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 적용 상품군 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>쿠폰 적용 상품군 선택</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 245;HEIGHT: 230;}
</STYLE>

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=code value="<?=$code?>">
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<!--tr>
		<td width="240"><p><input type=radio id="idx_codetype1" name=codetype value="ALL" <?=($codetype=="ALL"?"checked":"")?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_codetype1>모든 상품에 쿠폰 혜택이 적용.</label></p></td>
	</tr-->
	<tr>
		<td width="240"><p><input type=radio id="idx_codetype2" name=codetype value="CODE" <?=($codetype=="CODE"?"checked":"")?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_codetype2>일부 카테고리의 모든상품에만 적용.</label></p></td>
	</tr>
	<tr>
		<td width="240"><p><input type=radio id="idx_codetype3" name=codetype value="PRODUCT" <?=($codetype=="PRODUCT"?"checked":"")?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_codetype3>일부 상품에만 적용.</label></p></td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD>
	<table border=1 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td style="padding-top:1" nowrap>
		<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
		<DIV id=bodyList>
		<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td height=18 style="padding-left:5">
			<IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;background-color:<?=($code=="000000000000"?"#dddddd":"#ffffff")?>" onMouseOver="this.className='link_over'" onMouseOut="this.className='link_out'" onClick="ChangeSelect('out');">전체 상품군 선택</span>
			</td>
		</tr>
		<tr>
			<!-- 상품카테고리 목록 -->
			<td id="code_list" style="padding-right:5" nowrap>

			</td>
			<!-- 상품카테고리 목록 끝 -->
		</tr>
		</table>
		</DIV>
		</DIV>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD width="100%">
	<p align="center">
	<select name=prcode size=10 onChange="ChangeProduct();" style="width:100%;">
<?php
	if (strlen($code)==12) {
		$sql = "SELECT productcode,productname FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)
		WHERE b.c_category LIKE '{$code}%' ORDER BY date DESC";
		$result = pmysql_query($sql,get_db_conn());
		while ($row = pmysql_fetch_object($result)) {
			echo "<option value=\"{$row->productcode}\">".$row->productname.$sale;
		}
		echo "</option>\n";
	}
	pmysql_free_result($result);
?>
	</select></p>
	</TD>
</TR>
<TR>
	<TD height="25" style="padding-top:4pt;"><p align="center"><a href="javascript:CheckForm(document.form1);"><img src="images/btn_select1.gif" width="56" height="18" border="0" vspace="0" border=0></a><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></p></TD>
</TR>
</form>
</TABLE>

<?php
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' 
ORDER BY sequence DESC ";
include("codeinit.php");
?>
</body>
</html>