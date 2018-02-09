<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

//스킨 업로드 skin폴더 밑에 스킨 폴더 생성 후 압축해제
//적용,
//압축 풀기 후 디비에 추가 페이지 디비화
//tbldesignnewpage
//압축풀기


$upfile=$_FILES["upfile"];
$mode = $_POST["mode"];
#########################################################

//함수화
if($mode=="upload"){

	$ROOT = $_SERVER['DOCUMENT_ROOT']."/skin/";
	$file_name =  $ROOT.$upfile['name'];
	$file_type_check = explode('.',$upfile['name']);
	$dir_aja = $ROOT.$file_type_check[0];
	$dir_aja_a = $ROOT.$file_type_check[1];
	 if (!file_exists($file_name) && $dir_aja_a == "zip"){

			@copy($_FILES['upfile']['tmp_name'],$file_name);

			if(!is_dir($dir_aja)){
			//	mkdir($dir_aja);
			//	chmod($dir_aja,0777);
			//	exec("unzip $img_file_name -d $dir_aja");
			}else{
				echo "디렉토리 생성불가";
			}
	 }else{
		 echo "동일 파일명 있음";
	 }

}
?>
<script language="JavaScript">
function CheckForm() {
	document.skinupload.mode.value="upload";
	document.skinupload.submit();
}
</script>
<form name="skinupload"  method=post enctype="multipart/form-data">
<table>
<tr>
<input type=file name=upfile id=upfile>
<input type=hidden name=mode id=mode>
<img src="images/btn_fileup.gif" id="uploadButton" width="113" height="38" border="0" style="cursor:hand" onclick="CheckForm(document.skinupload);">
</tr>
</table>
</form>