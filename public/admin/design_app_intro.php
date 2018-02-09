<?php
/********************************************************************* 
// 파 일 명		: design_app_intro.php
// 설     명		: APP 인트로 이미지 관리
// 상세설명	: APP 인트로 이미지 관리
// 작 성 자		: 2016.03.30 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "de-9";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$type			= $_POST["type"];
	$file1			= $_FILES["up_file1"];
	$vimage1	= $_POST["vimage1"];

	$filePATH = $Dir.DataDir."shopimages/app/intro/";

	if ($type == "up") {
		if(ord($file1["name"])){
			
			if ( is_file($filePATH.$vimage1) ) {
				unlink($filePATH.$vimage1);
			}
			
			if (ord($file1['name']) && file_exists($file1['tmp_name'])) {
				$ext = strtolower(pathinfo($file1['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_file1 = "intro".".".$ext;
					move_uploaded_file($file1['tmp_name'], $filePATH.$up_file1);
					chmod($filePATH.$up_file1,0664);
				} else {
					$up_file1	="";
				}
			} 						
		}
		$onload="<script>window.onload=function(){alert(\"App intro 이미지 등록이 완료되었습니다.\");} </script>";
	}

	$vimage1	="";
	$def_vimage1	="";

	if (file_exists($filePATH."intro.gif")) $vimage1 ="intro.gif";
	if (file_exists($filePATH."intro.jpg")) $vimage1 ="intro.jpg";
	if (file_exists($filePATH."intro.png")) $vimage1 ="intro.png";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
_editor_url = "htmlarea/";
function CheckForm(){
	form1.type.value="up";
	form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; App 이미지 관리 &gt;<span>Intro 이미지관리</span></p></div></div>
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
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">App intro 이미지 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>App intro 이미지를 설정합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
				<col width=300>
				<col width=*>
				</colgroup>
                    <TR>
                        <th><span>App intro 이미지<br>&nbsp;&nbsp;&nbsp;<font color="#FF4C00">intro.('gif','jpg','png')</font></span></Th>
                        <TD class="td_con1">
							<input type=file name=up_file1 value=""  style="width:100%;">
							<span color=orange>'gif','jpg','png' 파일만 등록 가능합니다.</span><br />
							<input type=hidden name="vimage1" value="<?=$vimage1?>">
<?php
						if ($vimage1){
							echo "<br><img src='".$filePATH.$vimage1."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/app/intro/{$vimage1}' style=\"width:200px\">";
						}
?>
						</TD>
                    </TR>
				</table>
                </div>
				</td>
			</tr>			
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt>-</dt>
							<dd>-</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
<?=$onload?>
<?php 
include("copyright.php");
