<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}
//exdebug($_FILES);
$formname=$_POST["formname"];
$mode=$_POST["mode"];
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>엑셀파일 업로드</title>
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
		excel_submit();
		return false;
	}
}

function PageResize() {
	var oWidth = 450;
	var oHeight = document.all.table_body.clientHeight + 120;
	//var oHeight = 300;

	window.resizeTo(oWidth,oHeight);
}

function excel_submit() {
	if(document.form1.cvs_file.value=='') {
		alert("엑셀파일(CSV) 선택하세요.");
		document.form1.search.focus();
		return;
	}
	document.form1.submit();
}

function selectid(id) {
	opener.document[document.form1.formname.value].issue_excelmembers.value = document.form1.issue_excelmembers.value;
	var  issue_excelmembers_html = document.form1.issue_excelmembers.value;
	opener.document.getElementById("ID_membersLayer").innerHTML = " <img src='img/icon/table_bull.gif'> " + issue_excelmembers_html.replace(/,/gi, "<br> <img src='img/icon/table_bull.gif'> ");
	//alert(opener.document[document.form1.formname.value].issue_excelmembers.value);
	window.close();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>엑셀파일 업로드</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>엑셀파일 업로드</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
	<input type=hidden name=mode value="result">
	<input type=hidden name=formname value="<?=$formname?>">
	<?php if ($mode != 'result') {?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">엑셀파일(CSV) 업로드후 확인을 클릭하세요.</span> <a href='./sample/issue_member.sample.csv'>[엑셀샘플 다운로드 ]</a></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td><INPUT type=file size="24" name=cvs_file style="WIDTH:368px;height:22px"></td>
			<td width="40" align=right><a href="javascript:excel_submit();"><img src="images/btn_ok3.gif" border="0" valign=top></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	<?} else {?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:5pt;"><b><font color="black">회원내역</b>(회원내역을 확인하실수 있습니다.)</font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<tr>
			<th>번호</th>
			<th>아이디</th>
		</tr>
<?php
	$lineNumber	= 0;
	$mem_arr		= "";
	if($_FILES['cvs_file'][tmp_name]){
		$fp = fopen( $_FILES['cvs_file'][tmp_name], 'r' );
		while ( $record = fgetcsv( $fp, 135000, ',' ) ){
			if ($lineNumber > 0) {
				echo "<tr>\n";
				echo "	<td width=100 style='text-align:center;'>{$lineNumber}</td>\n";
				echo "	<td style='text-align:left;padding-left:5px'><span class=\"font_blue\"><B>{$record[0]}</B></span></td>\n";
				echo "</tr>\n";
				if ($mem_arr != '') $mem_arr	.= ",";
				$mem_arr	.= $record[0]; 
			}
			$lineNumber++;
		}
		fclose( $handle );
	}
?>							
		<input type=hidden name=issue_excelmembers value="<?=$mem_arr?>">		
		</table>
        </div>
		</td>
	</tr>
	<?php }?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center>
		<?if ($lineNumber > 0) {?><a href="javascript:selectid();"><img src="images/btn_input.gif"border="0" vspace="2" border=0></a>&nbsp;<?}?><a href="javascript:window.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a>
	</TD>
</TR>
</form>
</TABLE>
</body>
</html>
