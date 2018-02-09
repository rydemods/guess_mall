<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";
$filename="aboutdeliinfo.gif";
$filename_m="aboutdeliinfo_m.gif";

$mode=$_POST["mode"];

if($mode=="update") {
	$deliinfook=$_POST["deliinfook"];
	$deliinfotype=$_POST["deliinfotype"];

	if($deliinfotype!="IMAGE") {
		if(file_exists($imagepath.$filename)) {
			unlink($imagepath.$filename);
		}
	}

	if($deliinfotype=="TEXT") {
		$deliinfotext1 = pg_escape_string($_POST["deliinfotext1"]);
		$deliinfotext2 = pg_escape_string($_POST["deliinfotext2"]);
		$deli_info=$deliinfook."={$deliinfotype}={$deliinfotext1}=".$deliinfotext2;
	} else if($deliinfotype=="IMAGE") {
		//이미지 업로드 처리
		$up_image=$_FILES["deliinfoimage"];
		if ($up_image["size"]>153600) {
			alert_go('이미지 용량은 150KB를 넘을 수 없습니다.');
		}
		$ext = strtolower(pathinfo($up_image['name'],PATHINFO_EXTENSION));
		if (ord($up_image['name']) && $up_image["size"]>0 && in_array($ext,array('gif','jpg'))) {
			$up_image['name']=$filename;
			if(file_exists($imagepath.$filename)) {
				unlink($imagepath.$filename);
			}
			move_uploaded_file($up_image['tmp_name'],$imagepath.$up_image['name']);
			chmod($imagepath.$up_image['name'],0606);
		}

		$up_image_m=$_FILES["deliinfoimage_m"];
		if ($up_image_m["size"]>153600) {
			alert_go('이미지 용량은 150KB를 넘을 수 없습니다.');
		}
		$ext_m = strtolower(pathinfo($up_image_m['name'],PATHINFO_EXTENSION));
		if (ord($up_image_m['name']) && $up_image_m["size"]>0 && in_array($ext_m,array('gif','jpg'))) {
			$up_image_m['name']=$filename_m;
			if(file_exists($imagepath.$filename_m)) {
				unlink($imagepath.$filename_m);
			}
			move_uploaded_file($up_image_m['tmp_name'],$imagepath.$up_image_m['name']);
			chmod($imagepath.$up_image_m['name'],0606);
		}

		$deli_info=$deliinfook."=".$deliinfotype;
	} else if($deliinfotype=="HTML") {
		$deliinfohtml = pg_escape_string($_POST["deliinfohtml"]);
		$deliinfohtml_m = pg_escape_string($_POST["deliinfohtml_m"]);
		$deli_info=$deliinfook."={$deliinfotype}=".$deliinfohtml."=".$deliinfohtml_m;
	}

	if(ord($deli_info)) {
		$sql = "UPDATE tblshopinfo SET deli_info='{$deli_info}' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		$log_content = "## 입점업체 배송/교환/환불정보 수정 ## - 기본 - 노출여부 : ".$deliinfook." - 노출입력 : ".$deliinfotype;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.');}</script>";
	}
}

$sql = "SELECT deli_info FROM tblshopinfo";
$result=pmysql_query($sql,get_db_conn());
$_data=pmysql_fetch_object($result);
pmysql_free_result($result);

if(ord($_data->deli_info)) {
	$tempdeli_info=explode("=",$_data->deli_info);
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

if(ord($deliinfotype)==0) $deliinfotype="TEXT";

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.deliinfook[0].checked) {
		if(confirm("배송/교환/환불정보 노출을 사용함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.submit();
		}
	} else if(document.form1.deliinfook[1].checked) {
		if(confirm("배송/교환/환불정보 노출을 사용안함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
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

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>교환/배송/환불정보 노출</span></p></div></div>
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
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">교환/배송/환불정보 노출</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>배송/교환/환불정보 관련된 내용을 상품상세화면 하단에 공통적으로 노출할 수 있도록 설정하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배송/교환/환불정보 노출여부</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td>
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding="5" width="100%" border=0>
						<TR>
							<th><img src="images/product_exposure_img.gif" border="0"></th>
							<TD width="100%" class="td_con1" height="90">
                            <div class="table_none">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="font_orange"><input type=radio id="idx_deliinfook1" name=deliinfook value="Y"<?=($deliinfook=="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook1><b>교환/배송/환불정보 노출함</b></label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_deliinfook2" name=deliinfook value="N"<?=($deliinfook!="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook2><b>교환/배송/환불정보 노출안함</b></label></td>
							</tr>
							<tr>
								<td height=15></td>
							</tr>
							<tr>
								<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;교환/배송/환불정보 노출은 등록된 모든 상품에 적용됩니다.<br>
								&nbsp;&nbsp;상품 개별마다 노출안함은 "상품기타설정 > 배송/교환/환불정보 노출안함" 에서 설정 가능합니다.</td>
							</tr>
							</table>
                            </div>
							</TD>
						</TR>
						</TABLE>
                        </div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
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
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD><div class="point_title"><input type=radio id="idx_deliinfotype1" name="deliinfotype" value="TEXT" onclick="ChangeType('TEXT')" <?=($deliinfotype=="TEXT"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype1>텍스트로 노출정보 입력</label> (상품 배송/교환/환불 항목별로 텍스트 입력이 가능합니다.)</div></TD>
				</TR>
				<TR>
					<TD colspan="2">
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>배송정보</span></th>
						<TD class="td_con1">
                        <textarea name="deliinfotext1" rows="4" style="width:100%;" cols="20" disabled class="textarea"><?=$deliinfotext1?></textarea></td>
						</TD>
					</TR>
					<TR>
						<th><span>교환/환불정보</span></th>
						<TD class="td_con1">
						<textarea name="deliinfotext2" rows="4" style="width:100%;" cols="20" disabled class="textarea"><?=$deliinfotext2?></textarea></td>
						</TD>
					</TR>
					</TABLE>
                    </div>
					</TD>
				</tr>
				<tr>
					<TD><div class="point_title"><input type=radio id="idx_deliinfotype2" name="deliinfotype" value="IMAGE" onclick="ChangeType('IMAGE')"<?=($deliinfotype=="IMAGE"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype2>이미지로 노출정보 등록</label> (상품 배송/교환/환불정보를 이미지를 이용하여 노출하실 수 있습니다.)</div></TD>
				</tr>
				<tr>
					<TD colspan="2">
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>노출정보 이미지(PC)</span></th>
						<TD class="td_con1">
						<input type=file name="deliinfoimage" size=50><br>
						<!--
						<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" /> 
                        <input type=file name="deliinfoimage" style="width:400" disabled class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
						</div>						
						-->
						<font color="#FF6600">(150KB 미만, GIF/JPG파일)</font>
<?php
							if ($deliinfotype=="IMAGE") {
								if(file_exists($imagepath.$filename)) {
									$width=getimagesize($imagepath.$filename);
									if($width[0]>=585) $width=" width=585 ";
								}
								echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename."\" {$width}>\n";
							}
?>
						</TD>
					</TR>
					<TR>
						<th><span>노출정보 이미지(MOBILE)</span></th>
						<TD class="td_con1">
						<input type=file name="deliinfoimage_m" size=50><br>
						<font color="#FF6600">(150KB 미만, GIF/JPG파일)</font>
<?php
							if ($deliinfotype=="IMAGE") {
								if(file_exists($imagepath.$filename_m)) {
									$width=getimagesize($imagepath.$filename_m);
									if($width[0]>=585) $width=" width=585 ";
								}
								echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename_m."\" {$width}>\n";
							}
?>
						</TD>
					</TR>
					</TABLE>
                    </div>
					</TD>
				</tr>
				<TR>
					<TD><div class="point_title"><input type=radio id="idx_deliinfotype3" name="deliinfotype" value="HTML" onclick="ChangeType('HTML')"<?=($deliinfotype=="HTML"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype3>HTML로 노출정보 입력</label> (상품 배송/교환/환불정보를 html을 이용하여 입력하실 수 있습니다.)</div></TD>
				</TR>					
				<TR>
					<TD colspan="2">
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>배송/교환/환불정보(PC)</span></th>
						<TD class="td_con1">
						<textarea name="deliinfohtml" rows="15" STYLE="width:100%" cols="20" disabled class="textarea"><?=$deliinfohtml?></textarea>
						</TD>
					</TR>
					<TR>
						<th><span>배송/교환/환불정보(MOBILE)</span></th>
						<TD class="td_con1">
						<textarea name="deliinfohtml_m" rows="15" STYLE="width:100%" cols="20" disabled class="textarea"><?=$deliinfohtml_m?></textarea>
						</TD>
					</TR>
					</TABLE>
                    </div>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td colspan="2" align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
				</tr>
				</form>
				<tr><td height=20></td></tr>
				<tr>
					<td colspan="2">
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>[배송/교환/환불정보 노출함]으로 선택을 하여도, 각각의 상품상세정보 입력시 개별적으로 노출안함으로 설정할 수 있습니다.</span></dt>
						</dl>
						<dl>
							<dt><span>배송/교환/환불 정보는 상품상세페이지 상세설명 아래 출력되며, [텍스트/이미지/HTML] 선택하여 입력이 가능합니다.</span></dt>

						</dl>

						
					</div>
					</td>
				</tr>
				<tr>
					<td height="50" colspan="2"></td>
				</tr>
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
	</td>
</tr>
</table>
<script>ChangeType("<?=$deliinfotype?>");</script>
<?=$onload?>
<?php 
include("copyright.php");
