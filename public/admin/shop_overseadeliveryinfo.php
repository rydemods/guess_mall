<?php
/********************************************************************* 
// 파 일 명		: shop_overseadeliveryinfo.php 
// 설     명		: 해외배송 이미지 설정
// 상세설명	: 해외배송 이미지 설정 - (메뉴 상세, 상품 상세)
// 작 성 자		: 2015.11.10 - 김재수
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
	$PageCode = "sh-2";
	$MenuCode = "shop";
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
	$file2			= $_FILES["up_file2"];
	$vimage1	= $_POST["vimage1"];
	$vimage2	= $_POST["vimage2"];

	$filePATH = $Dir.DataDir."shopimages/overseadelivery/";

	if ($type == "up") {
		if(ord($file1["name"])){
			
			if ( is_file($filePATH.$vimage1) ) {
				unlink($filePATH.$vimage1);
			}
			
			if (ord($file1['name']) && file_exists($file1['tmp_name'])) {
				$ext = strtolower(pathinfo($file1['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_file1 = "overseadelivery1".".".$ext;
					move_uploaded_file($file1['tmp_name'], $filePATH.$up_file1);
					chmod($filePATH.$up_file1,0664);
				} else {
					$up_file1	="";
				}
			} 						
		}

		if(ord($file2["name"])){
			
			if ( is_file($filePATH.$vimage2) ) {
				unlink($filePATH.$vimage2);
			}
			
			if (ord($file2['name']) && file_exists($file2['tmp_name'])) {
				$ext = strtolower(pathinfo($file2['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_file2 = "overseadelivery2".".".$ext;
					move_uploaded_file($file2['tmp_name'], $filePATH.$up_file2);
					chmod($filePATH.$up_file2,0664);
				} else {
					$up_file2	="";
				}
			} 						
		}

		if(ord($file3["name"])){
			
			if ( is_file($filePATH.$vimage3) ) {
				unlink($filePATH.$vimage3);
			}
			
			if (ord($file3['name']) && file_exists($file3['tmp_name'])) {
				$ext = strtolower(pathinfo($file3['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_file3 = "overseadelivery1_m".".".$ext;
					move_uploaded_file($file3['tmp_name'], $filePATH.$up_file3);
					chmod($filePATH.$up_file3,0664);
				} else {
					$up_file3	="";
				}
			} 						
		}

		if(ord($file4["name"])){
			
			if ( is_file($filePATH.$vimage4) ) {
				unlink($filePATH.$vimage4);
			}
			
			if (ord($file4['name']) && file_exists($file4['tmp_name'])) {
				$ext = strtolower(pathinfo($file4['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_file4 = "overseadelivery2_m".".".$ext;
					move_uploaded_file($file4['tmp_name'], $filePATH.$up_file4);
					chmod($filePATH.$up_file4,0664);
				} else {
					$up_file4	="";
				}
			} 						
		}
		$onload="<script>window.onload=function(){alert(\"정보 등록이 완료되었습니다.\");} </script>";
	}

	$vimage1	="";
	$vimage2	="";
	$vimage3	="";
	$vimage4	="";

	if (file_exists($filePATH."overseadelivery1.gif")) $vimage1 ="overseadelivery1.gif";
	if (file_exists($filePATH."overseadelivery1.jpg")) $vimage1 ="overseadelivery1.jpg";
	if (file_exists($filePATH."overseadelivery1.png")) $vimage1 ="overseadelivery1.png";

	if (file_exists($filePATH."overseadelivery2.gif")) $vimage2 ="overseadelivery2.gif";
	if (file_exists($filePATH."overseadelivery2.jpg")) $vimage2 ="overseadelivery2.jpg";
	if (file_exists($filePATH."overseadelivery2.png")) $vimage2 ="overseadelivery2.png";

	if (file_exists($filePATH."overseadelivery1_m.gif")) $vimage3 ="overseadelivery1_m.gif";
	if (file_exists($filePATH."overseadelivery1_m.jpg")) $vimage3 ="overseadelivery1_m.jpg";
	if (file_exists($filePATH."overseadelivery1_m.png")) $vimage3 ="overseadelivery1_m.png";

	if (file_exists($filePATH."overseadelivery2_m.gif")) $vimage4 ="overseadelivery2_m.gif";
	if (file_exists($filePATH."overseadelivery2_m.jpg")) $vimage4 ="overseadelivery2_m.jpg";
	if (file_exists($filePATH."overseadelivery2_m.png")) $vimage4 ="overseadelivery2_m.png";
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>해외배송 이미지 설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
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
					<div class="title_depth3">해외배송 이미지 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>해외배송 메뉴 상세, 상품 상세 이미지를 설정합니다.</span></div>
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
                        <th><span>메뉴 상세 이미지(PC)<br>&nbsp;&nbsp;&nbsp;<font color="#FF4C00">overseadelivery1.('gif','jpg','png')</font></span></Th>
                        <TD class="td_con1">
							<input type=file name=up_file1 value=""  style="width:100%;">
							<span color=orange>'gif','jpg','png' 파일만 등록 가능합니다.</span><br />
							<input type=hidden name="vimage1" value="<?=$vimage1?>">
<?php
						if ($vimage1){
							echo "<br><img src='".$filePATH.$vimage1."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/overseadelivery/{$vimage1}' style=\"width:200px\">";
						} else {
							echo "<br><img src=images/space01.gif>";
						}
?>
						</TD>
                    </TR>
                    <TR>
                        <th><span>상품 상세 이미지(PC)<br>&nbsp;&nbsp;&nbsp;<font color="#FF4C00">overseadelivery2.('gif','jpg','png')</font></span></Th>
                        <TD class="td_con1">
							<input type=file name=up_file2 value=""  style="width:100%;">
							<span color=orange>'gif','jpg','png' 파일만 등록 가능합니다.</span><br />
							<input type=hidden name="vimage2" value="<?=$vimage2?>">
<?php
						if ($vimage2){
							echo "<br><img src='".$filePATH.$vimage2."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/overseadelivery/{$vimage2}' style=\"width:200px\">";
						} else {
							echo "<br><img src=images/space01.gif>";
						}
?>
						</TD>
                    </TR>
                    <TR>
                        <th><span>메뉴 상세 이미지(MOBILE)<br>&nbsp;&nbsp;&nbsp;<font color="#FF4C00">overseadelivery1_m.('gif','jpg','png')</font></span></Th>
                        <TD class="td_con1">
							<input type=file name=up_file3 value=""  style="width:100%;">
							<span color=orange>'gif','jpg','png' 파일만 등록 가능합니다.</span><br />
							<input type=hidden name="vimage3" value="<?=$vimage3?>">
<?php
						if ($vimage3){
							echo "<br><img src='".$filePATH.$vimage3."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/overseadelivery/{$vimage3}' style=\"width:200px\">";
						} else {
							echo "<br><img src=images/space01.gif>";
						}
?>
						</TD>
                    </TR>
                    <TR>
                        <th><span>상품 상세 이미지(MOBILE)<br>&nbsp;&nbsp;&nbsp;<font color="#FF4C00">overseadelivery2_m.('gif','jpg','png')</font></span></Th>
                        <TD class="td_con1">
							<input type=file name=up_file4 value=""  style="width:100%;">
							<span color=orange>'gif','jpg','png' 파일만 등록 가능합니다.</span><br />
							<input type=hidden name="vimage4" value="<?=$vimage4?>">
<?php
						if ($vimage4){
							echo "<br><img src='".$filePATH.$vimage4."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/overseadelivery/{$vimage4}' style=\"width:200px\">";
						} else {
							echo "<br><img src=images/space01.gif>";
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
