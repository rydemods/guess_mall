<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$rootlength=strlen($Dir)-1;	### /design/ 전 까지의 string length
$max=11;		### 전체경로의 디렉토리 "/" 갯수
$dirmax=20;		### 생성 가능한 디렉토리 갯수

$type=$_POST["type"];
$dir=$_POST["dir"];

$path="";

$originalpath = $Dir.DataDir."design/";
//$originalpath=str_replace("..","",$originalpath);
$originallength=strlen($originalpath);

if(ord($dir)==0)
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
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>파일다운로드</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = 630;
	var oHeight = 500;

	window.resizeTo(oWidth,oHeight);
}

function htmledit() {
	if (document.form1.filelist.selectedIndex==-1) {
		alert("파일목록에서 HTM(htm)파일을 선택하세요.");
		return;
	}
	filename = document.form1.filelist.options[document.form1.filelist.selectedIndex].value;
	ext = filename.substring(filename.length-3,filename.length);
	ext = ext.toLowerCase();
	if (ext!="htm" && ext!="css") {
		alert("htm,css파일만 편집하실 수 있습니다.");
		return;
	}
	window.open("about:blank","webftpetcpop","height=10,width=10");
	document.form3.action="design_webftp.edit.php";
	document.form3.val.value=filename;
	document.form3.submit();
}

function imageview() {
	if(document.form1.filelist.selectedIndex==-1) {
		alert("파일목록에서 이미지 파일을 선택하세요.");
		return;
	}
	filename = document.form1.filelist.options[document.form1.filelist.selectedIndex].value;
	ext = filename.substring(filename.length-3,filename.length);
	ext = ext.toLowerCase();
	if (ext!="gif" && ext!="jpg") {
		alert("GIF와 JPG파일만 보실 수 있습니다.");return;
	}
	window.open("about:blank","webftpetcpop","height=10,width=10");
	document.form3.action="design_webftp.imgview.php";
	document.form3.val.value=filename;
	document.form3.submit();
}

function select_file(filepath) {
	if(filepath.length>0) {
		document.all["fileurlidx"].innerHTML="/<?=RootPath.substr($subpath3,1)?>"+filepath;
	}
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>웹FTP 팝업으로 보기</p></div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<tr>
	<TD>
		<div class="point_title02">디렉토리</div>
    </TD>
	<TD>&nbsp;</TD>
	<TD><div class="point_title03">파일목록</span></div></TD>
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
					<IFRAME style="WIDTH:100%;HEIGHT:262px" src="design_webftp.directory.php?dir=<?=rtrim($subpath2,'/')?>&popup=ok" scrolling=yes size="6" marginwidth=5 marginheight=5></IFRAME>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>* <FONT color=#0054a6>현재 경로 : /<?=RootPath.substr($subpath,1)?></FONT></td>
		</tr>
		</table>
	<!-- 디렉토리 목록 끝 -->
	</td>

	<!-- -->
	<TD  align="center" valign=top style="padding-top:130"><img src="images/icon_nero.gif" border="0"></TD>
	<td align=center valign=top style="padding:5">
	<!-- 파일목록 시작 -->
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#0099CC">
		<tr>
			<td style="padding:8,8,0,8">
			<SELECT style="width:100%;" name=filelist size=16 multiple onChange="select_file(options.value)" class="font_size1">
<?php
				$temp=getFileList(rtrim($path,'/'));
				@sort($temp);
				for ($i=0;$i<sizeof($temp);$i++) {
					$filename=str_replace("*","",$temp[$i]);
					echo "<option value=\"$subpath2$filename\">$filename\n";
				}
				if ($i==0) echo "<option value=\"\">등록된 파일이 없습니다.";
?>
			</SELECT>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td align=center>
				<a href="javascript:htmledit()"><IMG SRC="images/design_webftp_icon1.gif" border="0"></a>
				</td>
				<td align=center>
				<a href="javascript:imageview();"><IMG SRC="images/design_webftp_icon2.gif" border="0"></a>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:5">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<col width=90></col>
		<col width=></col>
		<tr>
			<td>&nbsp;* 이미지경로 : </td>
			<td id=fileurlidx>선택안됨</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><FONT COLOR="#FF4C00">&nbsp;* 웹FTP목록창에는 파일추가,삭제를 지원하지 않습니다.</FONT></td>
	</tr>
	</table>
<!-- 파일목록 끝 -->
	</td>
</tr>
</form>
<TR>
	<TD align=center colspan="3">
	<a href="javascript:window.close()"><img src="images/btn_close.gif" border="0"></a>	
	</TD>
</TR>
</TABLE>

<form name=form3 method=post target="webftpetcpop">
<input type=hidden name=val>
</form>
</body>
</html>