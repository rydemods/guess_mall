<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-1";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$rootlength=strlen($Dir)-1;	### design/ 전 까지의 string length
$max=7;		### 전체경로의 디렉토리 "/" 갯수
$dirmax=20;		### 생성 가능한 디렉토리 갯수

$type=$_POST["type"];
$dir=$_POST["dir"];
$delfile=$_POST["delfile"];
$selectdir=$_POST["selectdir"];
$newdir=$_POST["newdir"];

$path="";

$upfile1=$_FILES['upfile1'];
$upfile2=$_FILES['upfile2'];
$upfile3=$_FILES['upfile3'];
$upfile4=$_FILES['upfile4'];
$upfile5=$_FILES['upfile5'];
$upfile6=$_FILES['upfile6'];
$upfile7=$_FILES['upfile7'];
$upfile8=$_FILES['upfile8'];
$upfile9=$_FILES['upfile9'];
$upfile10=$_FILES['upfile10'];

$originalpath = $Dir.DataDir."design/";
//$originalpath=str_replace("..","",$originalpath);

$originallength=strlen($originalpath);

$total=0;
getDirList(rtrim($originalpath,'/'));
@sort($dirlist);
$temp=$dirlist;
$number = sizeof($temp);
for($i=0;$i<$number;$i++) {
	$tempdir=$temp[$i];
	if(strlen($tempdir)>$originallength && is_dir($tempdir)) {
		$total++;
	}
}

if(ord($dir)==0)
	$path=$originalpath;
else {
	$dir=str_replace("..","",$dir); // 상위디렉토리로 이동 금지
	$dir=str_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
	$dir="";
}
if(strlen($path)<strlen($originalpath)) $path=$originalpath;

$countslash = explode("/", $path);

if($type=="ins") $max=$max-1;
if(count($countslash)>$max || ($type=="ins" && $total>=$dirmax)) {
	if($type=="ins" && $total>=$dirmax) {
		echo "<html><head><title></title></head><body onload=\"alert('디렉토리는 총 {$dirmax}개가 등록됩니다. 더이상 등록이 불가능합니다.');\"></body></html>";
	} else {
		echo "<html><head><title></title></head><body onload=\"alert('더이상 하위 디렉토리 등록이되지 않습니다.');\"></body></html>";
	}
	$type="";
	$temppath=rtrim($path,'/');
	$path = substr($temppath,0,strrpos($temppath,"/"))."/";
}
if($type=="del") {
	$temppath=rtrim($path,'/');
	proc_rmdir($temppath);
	$path = substr($temppath,0,strrpos($temppath,"/"))."/";
} else if($type=="mv") {
	$temppath=rtrim($path,'/');
	$path2 = substr($temppath,0,strrpos($temppath,"/"))."/";
	$path2 = $path2.$selectdir."/";

	if(is_dir($path2)) {
		echo "<html><head><title></title></head><body onload=\"alert('같은 이름의 디렉토리가 있습니다.');\"></body></html>";
	} else {
		rename($path,$path2);
		$path=$path2;
		$dir=substr($path,strlen($originalpath));
		$dir=rtrim($dir,'/');
	}
}

if(!is_dir($path) && $total<$dirmax) {
	mkdir($path);
	chmod($path, 0755);
} else if($type=="ins" && $total<$dirmax) {
	echo "<html><head><title></title></head><body onload=\"alert('등록된 디렉토리입니다.');\"></body></html>";
} else if(!is_dir($path)) {
	alert_go("디렉토리는 총 {$dirmax}개가 등록됩니다. 더이상 등록이 불가능합니다.",-1);
}

if ($type=="filedelete" && $delfile) {
	$delfile=substr($delfile,1);
	$ardelfile = explode("|",$delfile);
	$arnum = count($ardelfile);
	for($i=0;$i<$arnum;$i++)
		if(file_exists($originalpath.$ardelfile[$i])) unlink($originalpath.$ardelfile[$i]);
}

$dir2=rtrim($path,'/');
$dir2=substr($dir2,strrpos($dir2,"/")+1);

if($type=="fileupload") {
	$filearray = array (&$upfile1,&$upfile2,&$upfile3,&$upfile4,&$upfile5,&$upfile6,&$upfile7,&$upfile8,&$upfile9,&$upfile10);
	$filesize=(int)$filearray[0]["size"]+(int)$filearray[1]["size"]+(int)$filearray[2]["size"]+(int)$filearray[3]["size"]+(int)$filearray[4]["size"]+(int)$filearray[5]["size"]+(int)$filearray[6]["size"]+(int)$filearray[7]["size"]+(int)$filearray[8]["size"]+(int)$filearray[9]["size"];
	

	if($filesize>307200) {
		echo "<html><head><title></title></head><body onload=\"alert('파일용량이 300KByte를 초과되었습니다.');\"></body></html>";
	} else {
		$cnt= count($filearray);
		for($i=0;$i<$cnt;$i++){
			if (ord($filearray[$i]["name"]) && file_exists($filearray[$i]["tmp_name"])) {
				$filearray[$i]["name"]=str_replace(" ","",$filearray[$i]["name"]);
				$ext = strtolower(pathinfo($filearray[$i]["name"],PATHINFO_EXTENSION));
				if($ext =="html") {
					$filearray[$i]["name"] = rtrim($filearray[$i]["name"],'Ll');
				}
				else if ($ext=="php") $filearray[$i]["name"].="s";

				if($filearray[$i]["size"] > 307200) {
					echo "<html><head><title></title></head><body onload=\"alert('파일용량이 300KByte를 초과되었습니다.');\"></body></html>";
				} else {
					if(move_uploaded_file($filearray[$i]["tmp_name"],$path.$filearray[$i]["name"])) {
						chmod($path.$filearray[$i]["name"],0604);
					}
				}
			}
		}
	}

}

$subpath=substr($path,$rootlength);
$subpath3=substr($path,$rootlength,strlen($originalpath)-$rootlength);
$subpath2=substr($path,strlen($originalpath));
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function webftp_popup() {
	window.open("design_webftp.popup.php","webftppopup","height=10,width=10");
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
	window.open("about:blank","webftpetcpop","height=10,width=10,scrollbars=1");
	document.form3.action="design_webftp.imgview.php";
	document.form3.val.value=filename;
	document.form3.submit();
}

function delete_file() {
	val="";
	if(document.form1.filelist.selectedIndex==-1) {
		alert("삭제할 파일을 선택하세요.");
		return;
	}
	for(i=0;i<document.form1.filelist.options.length;i++) {
		if(document.form1.filelist.options[i].selected) {
			if(document.form1.filelist.options[i].value.length>0) {
				val+="|"+document.form1.filelist.options[i].value;
			}
		}
	}
	if(val.length==0) {
		alert("선택하신 폴더에 등록된 파일이 없습니다.");
		return;
	}
	if(confirm("선택된 파일 삭제하시겠습니까?")) {
		document.form2.type.value="filedelete";
		document.form2.delfile.value=val;
		document.form2.submit();
	}
}

function upfile_plus() {
	for(i=4;i<=10;i++) {
		if(document.all) {
			if(document.all["hideupfile"+i].style.display=="none") {
				document.all["hideupfile"+i].style.display="block";
				break;
			}
		} else if(document.getElementById) {
			if(document.getElementById("hideupfile"+i).style.display=="none") {
				document.getElementById("hideupfile"+i).style.display="block";
				break;
			}
		}
	}
}

function select_file(filepath) {
	if(filepath.length>0) {
		document.all["fileurlidx"].innerHTML="/<?=RootPath.substr($subpath3,1)?>"+filepath;
	}
}

function dir_delete() {
	if(document.form1.dir.value.length==0) {
		alert('기본 폴더는 삭제하실수 없습니다.');
		return;
	}
	if(!confirm("선택한 디렉토리를 삭제하시겠습니까?")) return;
	if(document.form1.count.value>0) {
		if(!confirm("선택한 디렉토리에 파일이나 디렉토리가 존재합니다.\n모두 삭제하시겠습니까?")) return;
	}
	document.form1.type.value="del";
	document.form1.submit();
}

function dir_modify() {
	if(document.form1.dir.value.length==0) {
		alert('기본 폴더는 수정이 불가능합니다.');
		return;
	}
	temp=document.form1.selectdir.value;
	count=0;
	for(i=0;i<temp.length;i++) {
		temp2=temp.substr(i,1);
		if((temp2>="0" && temp2<="9") || (temp2>="a" && temp2<="z") || (temp2>="A" && temp2<="Z") || temp2=="_" || temp2 =="-") {
			count++;
		} else {
			alert('디렉토리명에 올수 없는 이름입니다. 다시 입력하세요');
			document.form1.selectdir.focus();
			return;
		}
	}
	if (count>0) {
		if (!confirm("선택한 디렉토리명을 변경하시겠습니까?")) return;
		document.form1.type.value="mv";
		document.form1.submit();
	}
}

function dir_new() {
<?php
$countslash2 = explode("/", $subpath);
if(count($countslash2)>$max) {
?>
	alert('하위 폴더를 등록하실수가 없습니다.');
	return;
<?php }else{?>
	temp=document.form1.newdir.value;
	count=0;
	for(i=0;i<temp.length;i++) {
		temp2=temp.substr(i,1);
		if((temp2>="0" && temp2<="9") || (temp2>="a" && temp2<="z") || (temp2>="A" && temp2<="Z") || temp2=="_" || temp2=="-") {
			count++;
		} else{
			alert('디렉토리명에 올수 없는 이름입니다. 다시 입력하세요');
			document.form1.newdir.focus();
			return;
		}
	}
	if (count>0) {
		if(!confirm("디렉토리를 등록하시겠습니가?")) return;
	}
	if(document.form1.dir.value.length==0) document.form1.dir.value=temp;
	else document.form1.dir.value=document.form1.dir.value+"/"+temp;
	document.form1.type.value="ins";
	document.form1.submit();
<?php }?>
}

function upload_file() {
	if(confirm("파일을 업로드 하시겠습니까?")) {
		document.form1.type.value="fileupload";
		document.form1.submit();
	}
}

function download_file(path){
	window.open("about:blank","webftpetcpop","height=10,width=10");
	document.form3.action="design_webftp.down.php";
	document.form3.val.value=path;
	document.form3.submit();
}

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 웹FTP, 개별적용 선택 &gt;<span>웹FTP/웹FTP파일</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">웹FTP/웹 FTP팝업</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub"><span>쇼핑몰에 사용될 파일들을 웹상에서 쉽게 관리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=dir value="<?=rtrim($subpath2,'/')?>">
			<input type=hidden name=count value="<?=$count2?>">
			<input type=hidden name=filesize>
			<tr>
				<td align=right style="padding:0,2,5,0">
				<A HREF="javascript:webftp_popup()"><IMG src="images/webftp_button.gif" align=absmiddle border=0></A>
				</td>
			</tr>
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=310></col>
				<col width=50></col>
				<col width=></col>
				<tr>
					<td><div class="point_title02">디렉토리</div></td>
					<td>&nbsp;</td>
					<td><div class="point_title03">파일목록</div></td>
				</tr>

				<tr>
					<td align=center valign=top style="padding:5" bgcolor="#f8f8f8">
					<!-- 디렉토리 목록 시작 -->
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
						<table cellpadding="8" cellspacing="0" width="100%" bgcolor="#EBEBEB">
						<tr>
							<td align=center>
							<IFRAME style="WIDTH:100%;HEIGHT:262px" src="design_webftp.directory.php?dir=<?=rtrim($subpath2,'/')?>" scrolling=yes size="6" marginwidth=5 marginheight=5></IFRAME>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=10></td></tr>
					<tr>
						<td>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=65></col>
						<col width=></col>
						<col width=38></col>
						<col width=38></col>
						<tr>
							<td>
								<FONT color=#3d3d3d><img src="images/design_webftp_text01.gif" border="0"></FONT>
							</td>
							<td>
								<FONT color=#3d3d3d><INPUT style="width:99%" 
								size=32 name=selectdir class="input" 
								<?php
								if($originalpath!=$path) {
									echo "value=\"$dir2\"";
								} else {
									echo "disabled style=\"background='silver'\"";
								}
								?>
								></FONT>
							</td>
							<td>
								<FONT color=#3d3d3d><A href="javascript:dir_modify()"><IMG 
								src="images/icon_edit2.gif" align=absmiddle border=0></A> </FONT>
							</td>
							<td>
								<FONT color=#3d3d3d><A 
								href="javascript:dir_delete()"><IMG src="images/icon_del1.gif" align=absmiddle 
								border=0></A></FONT>
							</td>
						</tr>
						<tr>
							<td>
								<FONT color=#3d3d3d><img src="images/design_webftp_text02.gif" border="0"></FONT>
							</td>
							<td>
								<FONT color=#3d3d3d><INPUT size=32 name=newdir class="input" style=width:98%></FONT>
							</td>
							<td colspan=2>
								<FONT color=#3d3d3d><A 
								href="javascript:dir_new()"><IMG src="images/icon_newfolder.gif" align=absmiddle 
								border=0></A></FONT>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=1></td></tr>
					<tr>
						<td><hr size="1" color="#EBEBEB"></td>
					</tr>
					<tr>
						<td>* <FONT color=#0054a6>현재 경로 : /<?=RootPath.substr($subpath,1)?></FONT></td>
					</tr>
					<tr>
						<td><hr size="1" color="#EBEBEB"></td>
					</tr>
					</table>
					<!-- 디렉토리 목록 끝 -->
					</td>

					<!-- -->
					<TD  align="center" valign=top style="padding-top:130"><img src="images/icon_nero.gif" border="0"></TD>

					<td  align=center valign=top style="padding:5" bgcolor="#f8f8f8">
					<!-- 파일목록 시작 -->
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#0c71c6">
						<tr>
							<td style="padding:8,8,0,8">
							<SELECT style="width:100%;" name=filelist size=16 multiple onchange="select_file(this.value)" class="font_size1">
<?php
							$temp=getFileList(rtrim($path,'/'));
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
						<tr>
							<td>
							<table cellpadding="0" cellspacing="0" width="100%">
							<col width=33%></col>
							<col width=34%></col>
							<col width=33%></col>
							<tr>
								<td align=center>
									<a href="javascript:htmledit()"><IMG SRC="images/design_webftp_icon1.gif" border="0"></a>
								</td>
								<td align=center>
									<a href="javascript:imageview();"><IMG SRC="images/design_webftp_icon2.gif" border="0"></a>
								</td>
								<td align=center>
									<a href="javascript:delete_file()"><IMG SRC="images/design_webftp_icon3.gif" border="0"></a>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="30">
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
						<td style="padding-top:2pt; padding-bottom:2pt;">
						<table cellpadding="1" cellspacing="0" align="center" width="150">
						<tr>
							<td><A HREF="javascript:upload_file()"><img src="images/btn_upload.gif" border="0"></A></td>
							<td><a HREF="javascript:download_file('<?=rtrim($subpath2,'/')?>')"><img src="images/btn_download.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td align=center>

						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="18"><img src="images/design_webftp_fileup1.gif" border="0"></td>
							<td width="369" background="images/design_webftp_fileup2.gif"></td>
							<td width="19"><img src="images/design_webftp_fileup3.gif" border="0"></td>
						</tr>
						<tr>
							<td width="18" background="images/design_webftp_fileup4.gif"><p>&nbsp;</p></td>
							<td width="100%" height="60">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><a href="javascript:upfile_plus()"><img src="images/design_webftp_btnfileup.gif" border="0"></a></td>
							</tr>
							<tr><td height=3></td></tr>
							<tr>
								<td>
								<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
								<tr>
									<TD class="td_con1">
										<INPUT type="file" name="upfile1"><br />
									</td>
								</tr>
								<tr>
									<TD class="td_con1">
										<INPUT type="file" name="upfile2"><br />
									</td>
								</tr>
								<tr>
									<TD class="td_con1">
										<INPUT type="file" name="upfile3"><br />
									</td>
								</tr>
<?php
								for($i=4;$i<=10;$i++) {
									echo "<tr id=\"hideupfile{$i}\" style=\"display:none\">\n";
									echo "	<td class=\"td_con1\" width=\"600\">\n";
									//echo "	<input type=\"text\" id=\"fileName{$i}\" class=\"file_input_textbox w400\" readonly=\"readonly\">";
									//echo "	<div class=\"file_input_div\">";
									//echo "	<input type=\"button\" value=\"찾아보기\" class=\"file_input_button\" />";
									//echo "	<INPUT type=file name=upfile{$i} style=width:100% class=\"file_input_hidden\" onchange=\"javascript: document.getElementById('fileName{$i}').value = this.value\">\n";
									echo "	<INPUT type=file name=upfile{$i}>\n";
									//echo "	</div>";
									echo "	</td>\n";
									echo "</tr>\n";
								}
?>
								<tr>
									<td><img src="images/design_webftp_btntext.gif" border="0" vspace="2"></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
							<td width="19" background="images/design_webftp_fileup5.gif"><p>&nbsp;</p></td>
						</tr>
						<tr>
							<td width="18"><img src="images/design_webftp_fileup6.gif" border="0"></td>
							<td width="369" background="images/design_webftp_fileup7.gif"></td>
							<td width="19"><img src="images/design_webftp_fileup8.gif" border="0"></td>
						</tr>
						</table>

						</td>
					</tr>
					</table>
					<!-- 파일목록 끝 -->
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>웹FTP/웹FTP팝업</span></dt>
						<dd>- 각종 문서, 이미지 파일 등을 간편하게 웹상에서 업로드 및 삭제할 수 있습니다.<br />- html, css 파일은 웹FTP로 편집이 가능합니다.</dd>
					</dl>
					</div>

				</td>
			</tr>
			<tr><td height=50></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=type>
<input type=hidden name=dir value="<?=rtrim($subpath2,'/')?>">
<input type=hidden name=delfile>
</form>

<form name=form3 method=post target="webftpetcpop">
<input type=hidden name=val>
</form>

</table>
<?=$onload?>
<?php 
include("copyright.php");
