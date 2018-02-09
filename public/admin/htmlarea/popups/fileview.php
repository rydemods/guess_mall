<?php
$Dir="../../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$rootlength=strlen($Dir)-1;

$type=$_POST["type"];
$dir=$_POST["dir"];

$path="";

$originalpath = $Dir.DataDir."design/";
$originallength=strlen($originalpath);

if(strlen($dir)==0)
	$path=$originalpath;
else {
	$dir=str_replace("..","",$dir); // 상위디렉토리로 이동 금지
	$dir=str_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
	$dir="";
}
if(strlen($path)<strlen($originalpath)) $path=$originalpath;

$subpath=substr($path,$rootlength);
$subpath3=substr($path,$rootlength,strlen($originalpath)-$rootlength);
$subpath2=substr($path,strlen($originalpath));
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>이미지 지정</title>
<link rel="stylesheet" href="<?=$Dir.AdminDir?>style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 70;

	window.resizeTo(oWidth,oHeight);
}

function change(temp){
	document.form1.filename.value="/<?=RootPath.DataDir?>design/"+temp;
	newImg = new Image();
	newImg.src="/<?=RootPath.DataDir?>design/"+temp;
	if(newImg.width>210) document.form1.images.width=210;
	else document.form1.images.width=newImg.width;
	if(newImg.height>100) document.form1.images.height=100;
	else document.form1.images.height=newImg.height;
	document.form1.images.src=newImg.src;
	document.form1.images.style.display="";
}
function insertimage(){
	if(document.form1.filename.value.length==0 || document.form1.filelist.value=="") {
		alert('이미지를 선택하지 않으셨습니다.');
		document.form1.filelist.focus();
		return;
	}else {
		opener.document.all.txtFileName.value=document.form1.filename.value;
		window.close();
	}
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
<tr>
	<td>		
	<table cellpadding="0" cellspacing="0" width="100%">
	<col width=212></col>
	<col width=></col>
	<col width=20></col>
	<tr>
		<td><img src="<?=$Dir.AdminDir?>images/design_webftp_wintitle.gif" border="0"></td>
		<td background="<?=$Dir.AdminDir?>images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="<?=$Dir.AdminDir?>images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td style="padding:20">

	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<col width=240></col>
	<col width=50></col>
	<col width=></col>
	<tr><td colspan=3 height=1 background="<?=$Dir.AdminDir?>images/table_top_line.gif"></td></tr>
	<tr>
		<td class="table_cell" align="center"><FONT color=#3d3d3d><B>디렉토리</B></FONT></td>
		<td class="table_cell1" align="center">&nbsp;</td>
		<td class="table_cell1" align="center" background="<?=$Dir.AdminDir?>images/blueline_bg.gif"><b><font class=font_blue>파일목록</font></b></td>
	</tr>
	<TR>
		<TD colspan="3" background="<?=$Dir.AdminDir?>images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<tr>
		<td align=center valign=top style="padding:5">
		<!-- 디렉토리 목록 시작 -->
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td>
			<table cellpadding="8" cellspacing="0" width="100%" bgcolor="#EBEBEB">
			<tr>
				<td align=center>
				<IFRAME style="WIDTH:100%;HEIGHT:200px" src="directory.php?dir=<?=substr($subpath2,0,-1)?>" scrolling=yes size="6" marginwidth=5 marginheight=5></IFRAME>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>
		<tr>
			<td><hr size="1" color="#EBEBEB"></td>
		</tr>
		<tr>
			<td style="word-break:break-all;">* <FONT color=#0054a6>현재 경로 : /<?=RootPath.substr($subpath,1)?></FONT></td>
		</tr>
		<tr>
			<td><hr size="1" color="#EBEBEB"></td>
		</tr>
		</table>
		<!-- 디렉토리 목록 끝 -->
		</td>

		<!-- -->
		<TD class="td_con1" align="center" valign=top style="padding-top:130"><img src="<?=$Dir.AdminDir?>images/icon_nero.gif" border="0"></TD>

		<td class="td_con1" align=center valign=top style="padding:5">
		<!-- 파일목록 시작 -->
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#0099CC">
			<tr>
				<td style="padding:8,8,8,8">
				<SELECT style="width:100%;" name=filelist size=10 multiple onchange="change(options.value)" class="font_size1">
<?php
				$temp=getFileList(substr($path,0,-1));
				sort($temp);
				for ($i=0;$i<sizeof($temp);$i++) {
					$filename=str_replace("*","",$temp[$i]);
					echo "<option value=\"$subpath2$filename\">$filename\n";
					$tok = strtok("\n");
				}
				if ($i==0) echo "<option value=\"\">등록된 파일이 없습니다.";
?>
				</SELECT>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>
		<tr height=100>
			<td align=center valign=middle style="border:1px #dddddd solid">
			<img name=images style="display:none">
			</td>
		</tr>
		</table>
		<!-- 파일목록 끝 -->
		</td>
	</tr>
	<input type=hidden name=filename>
	</form>
	<tr><td colspan=3 height=1 background="<?=$Dir.AdminDir?>images/table_top_line.gif"></td></tr>
	</table>

	</td>
</tr>
<TR>
	<TD align=center>
		<a href="javascript:insertimage()"><img src=<?=$Dir.AdminDir?>images/btn_ok.gif border="0"></a><a href="javascript:window.close()"><img src="<?=$Dir.AdminDir?>images/btn_close.gif" border="0" hspace="10"></a>
	</TD>
</TR>
<tr><td height=10></td></tr>
</TABLE>

</body>
</html>