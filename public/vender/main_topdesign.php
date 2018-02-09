<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode=$_POST["mode"];

if($mode=="update") {
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfile=$_FILES["upfileimage"];

	$imagename=$Dir.DataDir."shopimages/vender/MAIN_".$_VenderInfo->getVidx().".gif";

	$iserror=false;
	if(strlen($toptype)==0) {
		$topdesign="";
		@unlink($imagename);
	} else if($toptype=="image") {
		$topdesign="";
		if($upfile['size'] < 102400) {
			if (strlen($upfile['name'])>0 && file_exists($upfile['tmp_name'])) {
				$ext = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg'))) {
					$imagenameorg="MAIN_".$_VenderInfo->getVidx().".gif";
					move_uploaded_file($upfile['tmp_name'],$Dir.DataDir."shopimages/vender/".$imagenameorg);
					chmod($Dir.DataDir."shopimages/vender/".$imagenameorg,0664);
				} else {
					echo "<html></head><body onload=\"alert('이미지 등록은 gif, jpg 파일만 등록 가능합니다.\\n\\n확인 후 다시 등록하시기 바랍니다.')\"></body></html>";exit;
				}
			} else {
				echo "<html></head><body onload=\"alert('이미지 선택이 안되었거나 잘못된 이미지 파일입니다.\\n\\n파일 확인 후 다시 등록하시기 바랍니다.')\"></body></html>";exit;
			}
		} else {
			echo "<html></head><body onload=\"alert('이미지 등록은 최대 100KB 까지 등록이 가능합니다.\\n\\n이미지 용량을 줄여서 다시 등록하시기 바랍니다.')\"></body></html>";exit;
		}
	} else if($toptype=="html") {
		if(strlen($topdesign)==0) {
			echo "<html></head><body onload=\"alert('본문 내용을 입력하세요.')\"></body></html>";exit;
		}
		@unlink($imagename);
	}
	$sql = "UPDATE tblvenderstore SET ";
	$sql.= "main_toptype	= '".$toptype."', ";
	$sql.= "main_topdesign	= '".$topdesign."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(pmysql_query($sql,get_db_conn())) {
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

$main_toptype=$_venderdata->main_toptype;
$main_topdesign=$_venderdata->main_topdesign;
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="PrdtDispInfoFucn.js.php" type="text/javascript"></script>
<script language="JavaScript">
var shop="layer1";
var ArrLayer = new Array ("layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementById(ArrLayer[i]).style.display="";
			else
				document.getElementById(ArrLayer[i]).style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	if(gbn=="layer1") {
		document.all["top_templt_img"].src="images/sample/display/top_design0.gif";
	} else {
		document.all["top_templt_img"].src="images/sample/display/top_design1.gif";
	}
	shop=gbn;
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
					<FONT COLOR="#ffffff"><B>메인 상단/이벤트 관리</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>메인 상단/이벤트 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 메인 상단에 눈에 띄게 알리고 싶은 내용이나 이벤트가 있을 경우 디자인을 추가해 주세요.</td>
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
			<tr><td height=10></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=iForm action="" method=post enctype="multipart/form-data">
				<input type=hidden name=mode>
				<input type=hidden name=preview_type value="main_topevent">
				<input type=hidden name=image_path value="">

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 상단/이벤트 관리</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td align=center bgcolor=F8F8F8 style=padding:25>
					<img id="top_templt_img" name="top_templt_img" src=images/sample/display/top_design<?=(strlen($main_toptype)==0?"0":"1")?>.gif border=0>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>편집타입 선택</B></td>
						<td width=83% style=padding:7,10>
						<input type=radio id="idx_toptype1" name=toptype value="" style="border:none" <?if(strlen($main_toptype)==0)echo"checked";?> onclick="ViewLayer('layer1')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype1>사용안함</label>
						<img width=10 height=0>
						<input type=radio id="idx_toptype2" name=toptype value="image" style="border:none" <?if($main_toptype=="image")echo"checked";?> onclick="ViewLayer('layer2')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype2>이미지</label>
						<img width=10 height=0>
						<input type=radio id="idx_toptype3" name=toptype value="html" style="border:none" <?if($main_toptype=="html")echo"checked";?> onclick="ViewLayer('layer3')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype3>HTML편집</label>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td colspan=2>
						<div id=layer1 style="margin-left:0;display:hide; display:<?=(strlen($main_toptype)==0?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">

						</div>
						<div id=layer2 style="margin-left:0;display:hide; display:<?=($main_toptype=="image"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>이미지 선택</B></td>
							<td width=83% style=padding:7,10>
							<input type=file name=upfileimage size=38 <?=$disabled?> class=button>
							</td>
						</tr>
						<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
						<tr>
							<td colspan=2>
<?php
							if($main_toptype=="image" && file_exists($Dir.DataDir."shopimages/vender/MAIN_".$_VenderInfo->getVidx().".gif")) {
								echo "<img src=\"".$Dir.DataDir."shopimages/vender/MAIN_".$_VenderInfo->getVidx().".gif\" border=0 width=100% height=100 align=absmiddle>";
							} else {
								echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
								echo "<tr><td height=100 bgcolor=#fafafa align=center>이미지를 등록하세요</td></tr>\n";
								echo "</table>\n";
							}
?>
							</td>
						</tr>
						</table>
						</div>
						<div id=layer3 style="margin-left:0;display:hide; display:<?=($main_toptype=="html"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td colspan=2>
							<textarea name=topdesign rows=8 cols=86 wrap=off style="width:100%" <?=$disabled?>><?=$main_topdesign?></textarea>
							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>

					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formEventSubmit('preview')"><img src="images/btn_preview01.gif" border=0></A>
					&nbsp;
					<A HREF="javascript:formEventSubmit('')"><img src="images/btn_save01.gif" border=0></A>
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php include("copyright.php"); ?>
