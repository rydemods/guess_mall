<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$imagepath=$Dir.DataDir."shopimages/vender/";
$filename="aboutdeliinfo_".$_VenderInfo->getVidx().".gif";
$filename_m="aboutdeliinfo_".$_VenderInfo->getVidx()."_m.gif";

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
			echo "<script>alert ('이미지 용량은 1M를 넘을 수 없습니다.');location.href='".$_SERVER[PHP_SELF]."';</script>\n";
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
			echo "<script>alert ('이미지 용량은 1M를 넘을 수 없습니다.');location.href='".$_SERVER[PHP_SELF]."';</script>\n";
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
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		pmysql_query($sql,get_db_conn());
			
		$log_content = "## 입점업체 배송/교환/환불정보 수정 ## - 노출여부 : ".$deliinfook." - 노출입력 : ".$deliinfotype;
		$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content);
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

if(strlen($_venderdata->deli_info)>0) {
	$tempdeli_info=explode("=",$_venderdata->deli_info);
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

if(strlen($_venderdata->re_addrinfo)>0) {
	$tempaddr_info=explode("|@|",$_venderdata->re_addrinfo);
	$re_zonecode=$tempaddr_info[0];
	$re_post1=$tempaddr_info[1];
	$re_post2=$tempaddr_info[2];
	$re_addr=$tempaddr_info[3];

}
include("header.php"); 
?>
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
function formSubmit() {
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

//-->
</SCRIPT>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
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
					<FONT COLOR="#ffffff"><B>배송/교환/환불정보 노출</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>배송/교환/환불정보 노출</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 배송/교환/환불 정보를 입점사 정책에 맞게 작성합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 배송/교환/환불 정보는 상품 상세정보 페이지 본문에 출력됩니다.</td>
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

				<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 배송/교환/환불정보 노출여부 </td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=230></col>
				<col width=></col>
				<tr>
					<td align=center><img src="images/deliinfo_img.gif" border=0></td>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr><td height=10></td></tr>
					<tr>
						<td align=center bgcolor=#F5F5F5 height=60>
						<input type=radio id="idx_deliinfook1" name=deliinfook value="Y" <?=($deliinfook=="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook1>배송/교환/환불정보 노출함</label>
						<img width=50 height=0>
						<input type=radio id="idx_deliinfook2" name=deliinfook value="N" <?=($deliinfook!="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook2>배송/교환/환불정보 노출안함</label>
						</td>
					</tr>
					<tr><td height=20></td></tr>
					<tr>
						<td style="line-height:13pt">
						<font style="font-size:8pt;color:#2A97A7"> &nbsp;&nbsp;[배송/교환/환불정보 노출함] 으로 선택을 하여도, 각각의 상품상세정보 입력시
						<br>&nbsp;&nbsp;개별적으로 정보 노출여부를 선택할 수 있습니다.</font>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 배송/교환/환불정보 입력</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=150></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype1" name="deliinfotype" value="TEXT" onclick="ChangeType('TEXT')" <?=($deliinfotype=="TEXT"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype1><B>텍스트로 노출정보 입력</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불 항목별로 텍스트 입력이 가능합니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>배송정보</td>
						<td bgcolor=#ffffff>
						<textarea name="deliinfotext1" style="width:100%;height:53" disabled><?=$deliinfotext1?></textarea>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>교환/환불정보</td>
						<td bgcolor=#ffffff>
						<textarea name="deliinfotext2" style="width:100%;height:53" disabled><?=$deliinfotext2?></textarea>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=150></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype2" name="deliinfotype" value="IMAGE" onclick="ChangeType('IMAGE')" <?=($deliinfotype=="IMAGE"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype2><B>이미지로 노출정보 등록</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불정보를 이미지를 이용하여 노출하실 수 있습니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>노출정보 이미지(PC)</td>
						<td bgcolor=#ffffff>
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
						<td align=center bgcolor=#ffffff>노출정보 이미지 선택(MOBILE)</td>
						<td bgcolor=#ffffff>
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
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=150></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype3" name="deliinfotype" value="HTML" onclick="ChangeType('HTML')" <?=($deliinfotype=="HTML"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype3><B>HTML로 노출정보 입력</B></label> <font style="font-size:8pt;color:#2A97A7">(상품 배송/교환/환불정보를 html을 이용하여 입력하실 수 있습니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>배송/교환/환불정보(PC)</td>
						<td bgcolor=#ffffff>
						<textarea name="deliinfohtml" style="width:100%;height:200" disabled><?=$deliinfohtml?></textarea>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>배송/교환/환불정보(MOBILE)</td>
						<td bgcolor=#ffffff>
						<textarea name="deliinfohtml_m" style="width:100%;height:200" disabled><?=$deliinfohtml_m?></textarea>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 반송주소</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>


				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=150></col>
					<col width=></col>
					<tr>
						<td align=center rowspan=2 bgcolor=#ffffff>반송받을 주소</td>
						<td bgcolor=#ffffff>
						<input type=text name="up_re_zonecode" id="up_re_zonecode" value="<?=$re_zonecode?>" size="5" maxlength="5" readonly>
						<input type=hidden name="up_re_post1" id="up_re_post1" value="<?=$re_post1?>"><input type=hidden name="up_re_post2" id="up_re_post2" value="<?=$re_post2?>"> <img src="images/btn_findpostno.gif" border=0 align=absmiddle style="cursor:hand" onClick="javascript:openDaumPostcode();">
						</td>
					</tr>
					<tr>
						<td bgcolor=#ffffff>
						<input type=text name="up_re_addr" id="up_re_addr" value="<?=$re_addr?>" size=75 maxlength=150>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>	

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

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
<script>ChangeType("<?=$deliinfotype?>");</script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php include("copyright.php"); ?>
