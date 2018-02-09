<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode=$_POST["mode"];
$cbm_tgbn=$_POST["cbm_tgbn"];
$cbm_sectcode=$_POST["cbm_sectcode"];
$cbm_themesectcode=$_POST["cbm_themesectcode"];

if($mode=="update") {
	$select_code=$_POST["select_code"];
	$select_tgbn=$_POST["select_tgbn"];
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfile=$_FILES["upfileimage"];

	if($select_tgbn=="10") {
		$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND productcode LIKE '".$select_code."%' AND display='Y' ";
	} else if($select_tgbn=="20") {
		$sql = "SELECT COUNT(*) as cnt FROM tblvenderthemeproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND themecode LIKE '".$select_code."%' ";
	}
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->cnt<=0) {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}

	$imagename=$Dir.DataDir."shopimages/vender/".$_VenderInfo->getVidx()."_CODE".$select_tgbn."_".$select_code.".gif";

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
					$imagenameorg=$_VenderInfo->getVidx()."_CODE".$select_tgbn."_".$select_code.".gif";
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

	$sql = "INSERT INTO tblvendercodedesign(
	vender			,
	code			,
	tgbn			,
	hot_used		,
	hot_dispseq		,
	hot_linktype	,
	code_toptype	,
	code_topdesign	) VALUES (
	'".$_VenderInfo->getVidx()."', 
	'".$select_code."', 
	'".$select_tgbn."', 
	'0', 
	'118', 
	'1', 
	'".$toptype."', 
	'".$topdesign."')";
	pmysql_query($sql,get_db_conn());

	if (pmysql_errno()==1062) {
		$sql = "UPDATE tblvendercodedesign SET ";
		$sql.= "code_toptype	= '".$toptype."', ";
		$sql.= "code_topdesign	= '".$topdesign."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND code='".$select_code."' AND tgbn='".$select_tgbn."' ";
		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}
}

if($cbm_tgbn!="10" && $cbm_tgbn!="20") {
	$cbm_tgbn="10";
	$cbm_sectcode="";
	$cbm_themesectcode="";
}

//기본 카테고리 조회
$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
$sql.= "AND display='Y' GROUP BY code_a ";
$result=pmysql_query($sql,get_db_conn());
$codelist="";
while($row=pmysql_fetch_object($result)) {
	$codelist.=$row->code_a.",";
}
pmysql_free_result($result);
$codelist=str_replace(',','\',\'',$codelist);
$CodeArr=array();
if(strlen($codelist)>0) {
	$sql = "SELECT code_a, code_name FROM tblproductcode WHERE code_a IN ('".$codelist."') AND code_b='000' AND code_c='000' AND code_d='000' ";
	$sql.= "ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$CodeArr[$row->code_a]=$row;
		if(strlen($cbm_sectcode)==0) {
			$cbm_sectcode=$row->code_a;
		}
	}
	pmysql_free_result($result);
}

//테마 카테고리 조회
$sql = "SELECT SUBSTR(a.themecode,1,3) as code_a FROM tblvenderthemeproduct a, tblproduct b ";
$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
$sql.= "AND b.display='Y' GROUP BY code_a ";
$result=pmysql_query($sql,get_db_conn());
$themecodelist="";
while($row=pmysql_fetch_object($result)) {
	$themecodelist.=$row->code_a.",";
}
pmysql_free_result($result);
$themecodelist=str_replace(',','\',\'',$themecodelist);
$ThemeCodeArr=array();
if(strlen($themecodelist)>0) {
	$sql = "SELECT code_a, code_name FROM tblvenderthemecode WHERE vender='".$_VenderInfo->getVidx()."' AND code_a IN ('".$themecodelist."') AND code_b='000' ";
	$sql.= "ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$ThemeCodeArr[$row->code_a]=$row;
		if(strlen($cbm_themesectcode)==0) {
			$cbm_themesectcode=$row->code_a;
		}
	}
	pmysql_free_result($result);
}

$code_toptype="";
$code_topdesign="";
if($cbm_tgbn=="10" && strlen($cbm_sectcode)>0) {
	$sql = "SELECT code_toptype,code_topdesign FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code='".$cbm_sectcode."' AND tgbn='10' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$code_toptype=$row->code_toptype;
		$code_topdesign=$row->code_topdesign;
	} else {
		$disabled="disabled";
	}
	pmysql_free_result($result);
	$select_code=$cbm_sectcode;
} else if($cbm_tgbn=="20" && strlen($cbm_themesectcode)>0) {
	$sql = "SELECT code_toptype,code_topdesign FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code='".$cbm_themesectcode."' AND tgbn='20' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$code_toptype=$row->code_toptype;
		$code_topdesign=$row->code_topdesign;
	} else {
		$disabled="disabled";
	}
	pmysql_free_result($result);
	$select_code=$cbm_themesectcode;
}

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
	<td width=175 valign=top nowrap><?php include ("menu.php"); ?></td>
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
					<FONT COLOR="#ffffff"><B>대분류 상단/이벤트 관리</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>대분류 상단/이벤트 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 분류 상단에 눈에 띄게 알리고 싶은 내용이나 이벤트가 있을 경우 디자인을 추가해 주세요.</td>
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
				<input type=hidden name=preview_type value="code_topevent">
				<input type=hidden name=image_path value="">
				<input type=hidden name=select_code value="<?=$select_code?>">
				<input type=hidden name=select_tgbn value="<?=$cbm_tgbn?>">

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
						<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>대분류</B></td>
						<td width=83% style=padding:7,10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td>
							<input type=radio value="10" name="cbm_tgbn" onClick='changeSect(10)' <?php if($cbm_tgbn=="10")echo"checked";?>>기본카테고리 
							<select name="cbm_sectcode" style=width:150 onChange='changeSect(10)'>
<?php
							$CodeArrVal=$CodeArr;
							while(list($key,$val)=each($CodeArrVal)) {
								echo "<option value=\"".$key."\"";
								if($key==$cbm_sectcode) echo " selected";
								echo ">".$val->code_name."</option>\n";
							}

							$ThemeCodeArrVal=$ThemeCodeArr;
?>
							</select>
							</td>
							<td width=20> </td>
							<td <?=(count($ThemeCodeArrVal)==0?"style=display:none":"")?>>
							<input type=radio value="20" name="cbm_tgbn" onClick='changeSect(20)' <?php if($cbm_tgbn=="20")echo"checked";?>> 테마 카테고리
							<select name="cbm_themesectcode" style=width:150 onChange='changeSect(20)'>
<?php
							while(list($key,$val)=each($ThemeCodeArrVal)) {
								echo "<option value=\"".$key."\"";
								if($key==$cbm_themesectcode) echo " selected";
								echo ">".$val->code_name."</option>\n";
							}
?>
							</select>
							</td>
						</tr>                      
						</table>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>편집타입 선택</B></td>
						<td width=83% style=padding:7,10>
						<input type=radio id="idx_toptype1" name=toptype value="" style="border:none" <?php if(strlen($code_toptype)==0)echo"checked";?> onclick="ViewLayer('layer1')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype1>사용안함</label>
						<img width=10 height=0>
						<input type=radio id="idx_toptype2" name=toptype value="image" style="border:none" <?php if($code_toptype=="image")echo"checked";?> onclick="ViewLayer('layer2')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype2>이미지</label>
						<img width=10 height=0>
						<input type=radio id="idx_toptype3" name=toptype value="html" style="border:none" <?php if($code_toptype=="html")echo"checked";?> onclick="ViewLayer('layer3')" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_toptype3>HTML편집</label>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td colspan=2>
						<div id=layer1 style="margin-left:0;display:hide; display:<?=(strlen($code_toptype)==0?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">

						</div>
						<div id=layer2 style="margin-left:0;display:hide; display:<?=($code_toptype=="image"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
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
							if(strlen($select_code)==3 && $code_toptype=="image" && file_exists($Dir.DataDir."shopimages/vender/".$_VenderInfo->getVidx()."_CODE".$cbm_tgbn."_".$select_code.".gif")) {
								echo "<img src=\"".$Dir.DataDir."shopimages/vender/".$_VenderInfo->getVidx()."_CODE".$cbm_tgbn."_".$select_code.".gif\" border=0 width=100% height=100 align=absmiddle>";
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
						<div id=layer3 style="margin-left:0;display:hide; display:<?=($code_toptype=="html"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td colspan=2>
							<textarea name=topdesign rows=8 cols=86 wrap=off style="width:100%" <?=$disabled?>><?=$code_topdesign?></textarea>
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
