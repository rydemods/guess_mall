<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode=$_POST["mode"];
if($mode=="update") {
	$up_brand_name=$_POST["up_brand_name"];
	$up_description=$_POST["up_description"];
	$upfile=$_FILES["upfile"];

	if(strlen($up_brand_name)==0) {
		echo "<html></head><body onload=\"alert('미니샵명을 입력하세요.');\"></body></html>";exit;
	}
	$sql = "SELECT COUNT(*) as cnt FROM tblvenderstore ";
	$sql.= "WHERE vender!='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND brand_name='".$up_brand_name."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$cnt=$row->cnt;
	pmysql_free_result($result);
	if($cnt>0) {
		echo "<html></head><body onload=\"alert('미니샵명이 중복되었습니다.\\n\\n미니샵명 중복확인을 하세요.');\"></body></html>";exit;
	}

	if(strlen($upfile["name"])>0 && $upfile["size"]>0 && file_exists($upfile["tmp_name"])) {
		$ext = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
		if(in_array($ext,array('gif','jpg'))){
			$imagenameorg="logo_".$_VenderInfo->getVidx().".gif";
			move_uploaded_file($upfile["tmp_name"],$Dir.DataDir."shopimages/vender/".$imagenameorg);
			chmod($Dir.DataDir."shopimages/vender/".$imagenameorg,0664);
		} else {
			echo "<html></head><body onload=\"alert('이미지 등록은 gif, jpg 파일만 등록 가능합니다.\\n\\n확인 후 다시 등록하시기 바랍니다.')\"></body></html>";exit;
		}
	}
	$sql = "UPDATE tblvenderstore SET ";
	$sql.= "brand_name			= '".$up_brand_name."', ";
	$sql.= "brand_description	= '".$up_description."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(pmysql_query($sql,get_db_conn())) {
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function formSubmit(proc_type) {
	if(iForm.up_brand_name.value.length==0) {
		alert("미니샵명을 입력하세요.");
		document.iForm.up_brand_name.style.background="#EEFFB6";
		document.iForm.up_brand_name.focus();
		return;
	}
	if(iForm.upfile.value.length>0){
		var local_img = iForm.upfile.value;
		if (!ValidImageFile(iForm.upfile)){
			alert("올바른 로고 이미지를 선택하세요.");
			iForm.upfile.focus();
			return;     
		}
		iForm.image_path.value=iForm.upfile.value;
	}

	if (proc_type=="preview") {
		mallWin = windowOpenScroll("", "MinishopPreview", 920, 500);
		mallWin.focus();

		iForm.target = "MinishopPreview";
		iForm.action = "preview.minishop.php";
		iForm.submit();
		iForm.action = "";
	} else {
		if (confirm("변경하신 내용을 저장하시겠습니까?")) {
			iForm.mode.value="update";
			iForm.target = "processFrame";
			iForm.submit();
		}
	}
}

function confirmMinishopName(name) {
	if(name.length==0) {
		alert("미니샵명을 입력하세요.");
		document.iForm.up_brand_name.style.background="#EEFFB6";
		document.iForm.up_brand_name.focus();
		return;
	}
	mallWin = windowOpenScroll("confirm_minishopname.php?brand_name="+name, "confirmMinishopName", 620, 400);
	mallWin.focus();
}
</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>미니샵 기본정보 설정</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>미니샵 기본정보 설정</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 기본정보를 설정하실 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 기본정보 입력시 미니샵 메인페이지 상단에 노출됩니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 로고 이미지는 500K미만 제한됩니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=iForm method=post action="<?=$_SERVER[PHP_SELF]?>" enctype=multipart/form-data>
				<input type=hidden name=preview_type value="info">
				<input type=hidden name=image_path>
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 입점업체 기본정보 <font color=#2A97A7>(미니샵 기본 정보를 수정하시면 미니샵 상단에 전시되는 정보가 변경됩니다. ['*'표시는 필수입력])</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>
				<table border=0 cellpadding=7 cellspacing=1 bgcolor=#dddddd width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B><font color=red>*</font> 미니샵명</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=text name=up_brand_name value="<?=$_venderdata->brand_name?>" size=20 maxlength=30 onKeyDown="chkFieldMaxLen(30)">
					<img src="images/btn_confirm10.gif" border=0 align=absmiddle style="cursor:hand;" onClick="confirmMinishopName(document.iForm.up_brand_name.value)">
					&nbsp;&nbsp;
					<font color=#2A97A7>* 한글 15자, 영문 30자 이내, 띄어쓰기 포함</font>
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B><font color=red>*</font> 미니샵 주소</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<B>http://<?=$minishopurl?></B>
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"> <B>미니샵 설명</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<textarea name=up_description style="width:100%;height:80px" onKeyDown="chkFieldMaxLen(200)"><?=$_venderdata->brand_description?></textarea>
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"> <B>미니샵 로고</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=file name=upfile size=20 class=button>
					&nbsp;&nbsp;
					<font color=#2A97A7>* 미등록시 아래 이미지가 로고로 등록됩니다.</font>
					<BR><img width=0 height=3><BR>
					<font color=#2A97A7>500K 미만의 JPG, GIF만 가능 / 크기:165 * 80</font>
					<br>
<?php
					if(file_exists($Dir.DataDir."shopimages/vender/logo_".$_VenderInfo->getVidx().".gif")) {
						echo "<img src=\"".$Dir.DataDir."shopimages/vender/logo_".$_VenderInfo->getVidx().".gif\" border=0>\n";
					} else {
						echo "<img src=\"".$Dir."images/minishop/logo.gif\" border=0>\n";
					}
?>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit('preview')"><img src="images/btn_preview01.gif" border=0></A>
					&nbsp;
					<A HREF="javascript:formSubmit('')"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<?=$onload?>
<?php include("copyright.php"); ?>
