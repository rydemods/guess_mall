<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
?>
<html>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=utf-8">
<head>
<title>상품리스트 디자인 선택</title>
<link rel="stylesheet" href="style.css">
<script language="JavaScript">
window.moveTo(10,10);
function choice(code,no) {
	if(confirm(no+'번 디자인을 선택하시겠습니까?')){
		try {
			opener.document.form1.up_list_type.value=code;
		} catch (e) {
			alert("오류로 인하여 디자인 선택이 안되었습니다.");
		}
		window.close();
	}
}
</script>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>상품진열 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>상품진열 선택</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<?php
$code=$_REQUEST["code"];
$gong=$_REQUEST["gong"];
if ($gong == "Y") {
	$sql = "SELECT code FROM tblproductdesigntype 
WHERE code LIKE 'BL%' ORDER BY code ASC ";

//	$sch="BL";
} else {
	$sql = "SELECT code FROM tblproductdesigntype 
WHERE code LIKE 'AL%'or code LIKE 'TEM%' ORDER BY code ASC ";
	//$sch="AL";
}

$result = pmysql_query($sql,get_db_conn());
$i=0;
while($row=pmysql_fetch_object($result)) {
	if (ord($code) && $code==$row->code) {
		$border=4;
		$choice_img = "<img  src=images/btn_selecton.gif align=absmiddle>";
	} else {
		$border=1;
		$choice_img = "<a href=\"JavaScript:choice('{$row->code}','".($i+1)."')\"><img src=images/btn_selectout.gif border=0 align=absmiddle></a>";
	}
	if($i%2==0) echo "<tr>\n";
	echo "	<TD style=\"padding:10pt;\">\n";
	echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td>\n";
	echo "		<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"150\">\n";
	echo "		<tr>\n";
	echo "			<td align=center><img src=\"images/product/{$row->code}.gif\" width=\"150\" height=\"150\" border=\"0\" style=\"border-width:{$border}pt; border-color:rgb(222,222,222); border-style:solid;\"></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td height=4></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td>\n";
	echo "			<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"135\">\n";
	echo "			<tr>\n";
	echo "				<td>".($i+1)."번스킨 {$choice_img}</td>\n";
	echo "			</tr>\n";
	echo "			</table>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		</table>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</TD>\n";
	if($i%2==1) echo "</tr>\n";
	$i++;
}
pmysql_free_result($result);
?>
<TR>
	<TD colspan=2><hr size="1" noshade color="#EBEBEB"></TD>
</TR>
<TR>
	<TD colspan=2 align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="5" border=0 hspace="2"></a></TD>
</TR>
</TABLE>
</body>
</html>
