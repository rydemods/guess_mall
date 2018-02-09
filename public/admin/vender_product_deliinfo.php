<?php
/********************************************************************* 
// 파 일 명		: vender_product_deliinfo.php 
// 설     명		: 입점업체 배송/교환/환불정보 노출
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 배송/교환/환불정보 노출여부
// 작 성 자		: 2016.03.18 - 김재수
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
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "me-4";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];

	$sql = "SELECT a.*, b.brand_name, b.deli_info, b.re_addrinfo FROM tblvenderinfo a, tblvenderstore b ";
	$sql.= "WHERE a.vender='{$vender}' AND a.delflag='N' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	}
	pmysql_free_result($result);	

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

$imagepath=$Dir.DataDir."shopimages/vender/";
$filename="aboutdeliinfo_".$vender.".gif";
$filename_m="aboutdeliinfo_".$vender."_m.gif";

$mode=$_POST["mode"];

if($mode=="update") {
	$deliinfook=$_POST["deliinfook"];
	$deliinfotype=$_POST["deliinfotype"];
	$up_re_zonecode=$_POST["up_re_zonecode"];
	$up_re_post1=$_POST["up_re_post1"];
	$up_re_post2=$_POST["up_re_post2"];
	$up_re_addr=$_POST["up_re_addr"];

	if ($up_re_zonecode && $up_re_addr) {
		$re_addrinfo = $up_re_zonecode."|@|".$up_re_post1."|@|".$up_re_post2."|@|".$up_re_addr;
	}

	if($deliinfotype!="IMAGE") {
		if(file_exists($imagepath.$filename)) {
			unlink($imagepath.$filename);
		}
		if(file_exists($imagepath.$filename_m)) {
			unlink($imagepath.$filename_m);
		}
	}

	if($deliinfotype=="TEXT") {
		$deliinfotext1=$_POST["deliinfotext1"];
		$deliinfotext2=$_POST["deliinfotext2"];
		$deli_info=$deliinfook."=".$deliinfotype."=".$deliinfotext1."=".$deliinfotext2;
	} else if($deliinfotype=="IMAGE") {
		//이미지 업로드 처리
		$up_image=$_FILES["deliinfoimage"];
		if ($up_image["size"]>1024000) {
			echo "<script>alert ('이미지 용량은 1M를 넘을 수 없습니다.');parent.location.reload();</script>\n";
			exit;
		}
		$ext = strtolower(pathinfo($up_image["name"],PATHINFO_EXTENSION));
		if (strlen($up_image['name'])>0 && $up_image["size"]>0 && in_array($ext,array('gif','jpg'))) {
			$up_image['name']=$filename;
			if(file_exists($imagepath.$filename)) {
				unlink($imagepath.$filename);
			}
			move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image['name']);
			chmod($imagepath.$up_image['name'],0606);
		}

		$up_image_m=$_FILES["deliinfoimage_m"];
		if ($up_image_m["size"]>1024000) {
			echo "<script>alert ('이미지 용량은 1M를 넘을 수 없습니다.');parent.location.reload();</script>\n";
			exit;
		}
		$ext_m = strtolower(pathinfo($up_image_m["name"],PATHINFO_EXTENSION));
		if (strlen($up_image_m['name'])>0 && $up_image_m["size"]>0 && in_array($ext_m,array('gif','jpg'))) {
			$up_image_m['name']=$filename_m;
			if(file_exists($imagepath.$filename_m)) {
				unlink($imagepath.$filename_m);
			}
			move_uploaded_file($up_image_m['tmp_name'],$imagepath.$up_image_m['name']);
			chmod($imagepath.$up_image_m['name'],0606);
		}

		$deli_info=$deliinfook."=".$deliinfotype;
	} else if($deliinfotype=="HTML") {
		$deliinfohtml=$_POST["deliinfohtml"];
		$deliinfohtml_m=$_POST["deliinfohtml_m"];
		$deli_info=$deliinfook."=".$deliinfotype."=".$deliinfohtml."=".$deliinfohtml_m;
	}

	if(strlen($deli_info)>0) {
		$sql = "UPDATE tblvenderstore SET deli_info='".$deli_info."',re_addrinfo='".$re_addrinfo."' ";
		$sql.= "WHERE vender='".$vender."' ";
		pmysql_query($sql,get_db_conn());

		$log_content = "## 입점업체 배송/교환/환불정보 수정 ## - 업체ID : ".$_vdata->id." - 노출여부 : ".$deliinfook." - 노출입력 : ".$deliinfotype;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		echo "<html></head><body onload=\"alert('배송/교환/환불정보 노출설정이 완료되었습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('배송/교환/환불정보 노출설정중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

if(strlen($_vdata->deli_info)>0) {
	$tempdeli_info=explode("=",$_vdata->deli_info);
	$deliinfook=$tempdeli_info[0];
	$deliinfotype=$tempdeli_info[1];
	if($deliinfotype=="TEXT") {
		$deliinfotext1=$tempdeli_info[2];
		$deliinfotext2=$tempdeli_info[3];
	} else if($deliinfotype=="HTML") {
		$deliinfohtml=$tempdeli_info[2];
		$deliinfohtml_m=$tempdeli_info[3];
	}
} else {
	$deliinfook="N";
	$deliinfotype="TEXT";
}
if(strlen($deliinfotype)==0) $deliinfotype="TEXT";

if(strlen($_vdata->re_addrinfo)>0) {
	$tempaddr_info=explode("|@|",$_vdata->re_addrinfo);
	$re_zonecode=$tempaddr_info[0];
	$re_post1=$tempaddr_info[1];
	$re_post2=$tempaddr_info[2];
	$re_addr=$tempaddr_info[3];

}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search=$_POST["search"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script><!-- 다음 우편번호 api -->
<script>
// 다음 우편번호 팝얻창 불러오기
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('up_re_zonecode').value = data.zonecode;
			document.getElementById('up_re_post1').value = data.postcode1;
			document.getElementById('up_re_post2').value = data.postcode2;
			document.getElementById('up_re_addr').value = data.address;
			document.getElementById('up_re_addr').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}
</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.deliinfook[0].checked) {
		if(confirm("배송/교환/환불정보 노출을 사용함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.target="processFrame";
			document.form1.submit();
		}
	} else if(document.form1.deliinfook[1].checked) {
		if(confirm("배송/교환/환불정보 노출을 사용안함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.target="processFrame";
			document.form1.submit();
		}
	}
}
function ChangeType(type){
	if (type=="TEXT") {
		document.form1.deliinfotext1.disabled=false;
		document.form1.deliinfotext2.disabled=false;
		document.form1.deliinfoimage.disabled=true;
		document.form1.deliinfoimage_m.disabled=true;
		document.form1.deliinfohtml.disabled=true;
		document.form1.deliinfohtml_m.disabled=true;
	} else if(type=="IMAGE") {
		document.form1.deliinfotext1.disabled=true;
		document.form1.deliinfotext2.disabled=true;
		document.form1.deliinfoimage.disabled=false;
		document.form1.deliinfoimage_m.disabled=false;
		document.form1.deliinfohtml.disabled=true;
		document.form1.deliinfohtml_m.disabled=true;
	} else if(type=="HTML") {
		document.form1.deliinfotext1.disabled=true;
		document.form1.deliinfotext2.disabled=true;
		document.form1.deliinfoimage.disabled=true;
		document.form1.deliinfoimage_m.disabled=true;
		document.form1.deliinfohtml.disabled=false;
		document.form1.deliinfohtml_m.disabled=false;
	}
}

function GoReturn() {
	document.form3.submit();
}


function goBackList(){
	location.href="vender_management2.php";
}


//-->
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 입점업체 정보관리 &gt;<span>배송/교환/환불정보 노출</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">배송/교환/환불정보 노출</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>배송/교환/환불 정보를 입점사 정책에 맞게 작성합니다.</span></div>
				</td>
			</tr>
			<tr><td height=15></td></tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>업체 ID</span></th>
					<td><B><?=$_vdata->id?></B></td>
				</tr>
				<tr>
					<th><span>상호 (회사명)</span></th>
					<td><?=$_vdata->com_name?></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<input type=hidden name=vender value="<?=$vender?>">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송/교환/환불정보 노출여부</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>노출여부</span></th>
					<td>
						<input type=radio id="idx_deliinfook1" name=deliinfook value="Y" <?=($deliinfook=="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook1>노출함</label>
						<input type=radio id="idx_deliinfook2" name=deliinfook value="N" <?=($deliinfook!="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook2>노출안함</label><br>
						<font style="font-size:8pt;color:#2A97A7"> &nbsp;&nbsp;[배송/교환/환불정보 노출함] 으로 선택을 하여도, 각각의 상품상세정보 입력시 개별적으로 정보 노출여부를 선택할 수 있습니다.</font>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송/교환/환불정보 입력</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<td colspan=2 bgcolor=#F5F5F5 style='border-left:1px solid #b9b9b9;'>
					<input type=radio id="idx_deliinfotype1" name="deliinfotype" value="TEXT" onclick="ChangeType('TEXT')" <?=($deliinfotype=="TEXT"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype1><B>텍스트로 노출정보 입력</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불 항목별로 텍스트 입력이 가능합니다.)</font>
					</td>
				</tr>
				<tr>
					<th><span>배송정보</span></th>
					<td><textarea name="deliinfotext1" style="width:100%;height:53" disabled><?=$deliinfotext1?></textarea></td>
				</tr>
				<tr>
					<th><span>교환/환불정보</span></th>
					<td><textarea name="deliinfotext2" style="width:100%;height:53" disabled><?=$deliinfotext2?></textarea></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>		
			<tr><td height=5></td></tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<td colspan=2 bgcolor=#F5F5F5 style='border-left:1px solid #b9b9b9;'>
					<input type=radio id="idx_deliinfotype2" name="deliinfotype" value="IMAGE" onclick="ChangeType('IMAGE')" <?=($deliinfotype=="IMAGE"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype2><B>이미지로 노출정보 등록</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불정보를 이미지를 이용하여 노출하실 수 있습니다.)</font>
					</td>
				</tr>
				<tr>
					<th><span>노출정보 이미지(PC)</span></th>
					<td>
						<input type=file name="deliinfoimage" style="width:350" class="button" disabled> <font style="font-size:8pt;color:#2A97A7">(1M 미만, GIF/JPG파일)</font>
<?
						if ($deliinfotype=="IMAGE") {
							if(file_exists($imagepath.$filename)) {
								$width=getimagesize($imagepath.$filename);
								if($width[0]>=490) $width=" width=490 ";
							}
							echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename."\" ".$width.">\n";
						}
?>
						</td>
				</tr>
				<tr>
					<th><span>노출정보 이미지(MOBILE)</span></th>
					<td>
						<input type=file name="deliinfoimage_m" style="width:350" class="button" disabled> <font style="font-size:8pt;color:#2A97A7">(1M 미만, GIF/JPG파일)</font>
<?
						if ($deliinfotype=="IMAGE") {
							if(file_exists($imagepath.$filename_m)) {
								$width=getimagesize($imagepath.$filename_m);
								if($width[0]>=490) $width=" width=490 ";
							}
							echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename_m."\" ".$width.">\n";
						}
?>
						</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>				
			<tr><td height=5></td></tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<td colspan=2 bgcolor=#F5F5F5 style='border-left:1px solid #b9b9b9;'>
					<input type=radio id="idx_deliinfotype3" name="deliinfotype" value="HTML" onclick="ChangeType('HTML')" <?=($deliinfotype=="HTML"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype3><B>HTML로 노출정보 입력</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불정보를 html을 이용하여 입력하실 수 있습니다.)</font>
					</td>
				</tr>
				<tr>
					<th><span>배송/교환/환불정보(PC)</span></th>
					<td><textarea name="deliinfohtml" style="width:100%;height:200" disabled><?=$deliinfohtml?></textarea></td>
				</tr>
				<tr>
					<th><span>배송/교환/환불정보(MOBILE)</span></th>
					<td><textarea name="deliinfohtml_m" style="width:100%;height:200" disabled><?=$deliinfohtml_m?></textarea></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">반송주소</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th rowspan=2><span>반송받을 주소</span></th>
					<td>
					<input type=text name="up_re_zonecode" id="up_re_zonecode" value="<?=$re_zonecode?>" size="5" maxlength="5" readonly>
					<input type=hidden name="up_re_post1" id="up_re_post1" value="<?=$re_post1?>"><input type=hidden name="up_re_post2" id="up_re_post2" value="<?=$re_post2?>"> <img src="images/order_no_uimg.gif" border=0 align=absmiddle style="cursor:hand" onClick="javascript:openDaumPostcode();">
					</td>
				</tr>
				<tr>
					<td>
					<input type=text name="up_re_addr" id="up_re_addr" value="<?=$re_addr?>" size=75 maxlength=150>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>				
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
					<a href="javascript:CheckForm();"><img src="images/btn_edit2.gif" width="113" height="38" border="0"></a>
					&nbsp;
					<a href="javascript:goBackList();"><img src="img/btn/btn_list.gif"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>배송/교환/환불정보 노출</span></dt>
							<dd>- 배송/교환/환불 정보를 입점사 정책에 맞게 작성합니다.<br>
							- 배송/교환/환불 정보는 상품 상세정보 페이지 본문에 출력됩니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name="form3" method="post" action="vender_management2.php">
			<input type=hidden name='vender' value="<?=$value?>">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
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
<script>ChangeType("<?=$deliinfotype?>");</script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
