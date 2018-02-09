<?php
$Dir="../../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$rootlength=strlen($Dir)-1;

$dir=$_REQUEST["dir"];
$popup=$_REQUEST["popup"];

$dir=str_replace("..","",$dir);

$originalpath = $Dir.DataDir."design/";
$originallength=strlen($originalpath);

if(strlen($dir)==0)
	$path=$originalpath;
else {
	$dir=str_replace("..","",$dir); // 상위디렉토리로 이동 금지
	$dir=str_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
}
if(strlen($path)<strlen($originalpath)) $path=$originalpath;

$count=0;
getDirList(rtrim($originalpath,'/'));
@sort($dirlist);
$temp=$dirlist;
$number = sizeof($temp);
for($i=0;$i<$number;$i++) {
	$tempdir=$temp[$i];
	if(strlen($tempdir)>$originallength && is_dir($tempdir)){
		$tempdirectory[$count++] = substr($tempdir,$originallength);
	}
}

$back=0;
for($i=$count-1;$i>=0;$i--) {
	$num = strpos($tempdirectory[$i+1],"/");
	if($num>0) $len=$num;
	else $len=strlen($tempdirectory[$i+1]);

	$num2 = strpos($tempdirectory[$i-1],"/");
	if($num2>0) $len2=$num2;
	else $len2=strlen($tempdirectory[$i-1]);

	$filenum[$i]=1; 
	$filefolder[$i]=count(explode("/",$tempdirectory[$i]));
	if($filefolder[$i]==1) { //1,2
		if($i==($count-1) || $back==2) {
			$filenum[$i]=1;
			$back=1;
		} else $filenum[$i]=1;
	} else if($filefolder[$i]==2) { //3,4,5,6
		if(strcmp(substr($tempdirectory[$i+1],0,$len),substr($tempdirectory[$i],0,$len))==0) {
			if($i==$count-1) $filenum[$i]=4;
			else if((strcmp(substr($tempdirectory[$i-1],0,$len2),substr($tempdirectory[$i],0,$len2))==0 && $filefolder[$i]==$filefolder[$i-1]) || (strcmp(substr($tempdirectory[$count-1],0,$len2),substr($tempdirectory[$i],0,$len2))==0)) { 
				$filenum[$i]=4;
			} else $filenum[$i]=4;
		} else if($filefolder[$i]!=$filefolder[$i-1]) $filenum[$i]=4;
	}
   if($back==0 && $back<$filefolder[$i]) $back=$filefolder[$i];

   if(strpos($tempdirectory[$i],"/")!=0) $directory[$i]=substr($tempdirectory[$i],strrpos($tempdirectory[$i],"/")+1);
   else $directory[$i]=$tempdirectory[$i];
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>디렉토리 목록</title>
<link rel="stylesheet" href="<?=$Dir.AdminDir?>style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(dir) {
	document.form1.dir.value=dir;
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<form name=form1 method=post action="fileview.php" target="_parent">
<tr>
	<td class="font_size1" style="padding:3">
	<img src="<?=$Dir.AdminDir?>images/directory_root.gif"> <a href="javascript:document.form1.submit()"><?if($originalpath==$path)echo"<font color=#FF4C00>"; echo "/".RootPath.substr(substr($originalpath,$rootlength),1)."</font>";?></a>
	</td>
</tr>
<?php
for($i=0;$i<$count;$i++) {
	echo "<tr>\n";
	echo "	<td class=\"font_size1\" style=\"padding:0,3,0,".(3+($filenum[$i]-1)*6)."\">\n";
	echo "	<img src=\"".$Dir.AdminDir."images/directory_folder".(strcmp($path,$originalpath.$tempdirectory[$i]."/")==0?"2":"1").".gif\" align=absmiddle> <a href=\"javascript:CheckForm('".$tempdirectory[$i]."')\">".(strcmp($path,$originalpath.$tempdirectory[$i]."/")==0?"<font color=#FF4C00>":"").$directory[$i]."</font></a>\n";
	echo "	</td>\n";
	echo "</tr>\n";
}
?>
<input type=hidden name=dir>
</form>
</table>
</body>
</html>