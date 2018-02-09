<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$type=$_POST["type"];
$code=$_POST["code"];
$sch=$_POST["sch"];
$detail_type=$_POST["detail_type"];
$design=$_POST["design"];
$is_design=$_POST["is_design"];

if(ord($sch)==0) {
	if(ord($detail_type)==0) {
		$sch="AD";
	} else {
		$sch=substr($detail_type,0,2);
	}
}

if($sch!="AD" && $sch!="BD") $sch="AD";

$code_a=substr($code,0,3);
$code_b=substr($code,3,3);
$code_c=substr($code,6,3);
$code_d=substr($code,9,3);

if($mode=="update" && ord($design) && strlen($code)==12 && $code!="000000000000") {
	$sql = "UPDATE tblproductcode SET detail_type='{$design}' 
	WHERE code_a='{$code_a}' ";
	if($is_design=="1") {
		if($code_b!="000") {
			$sql.= "AND code_b='{$code_b}' ";
			if($code_c!="000") {
				$sql.= "AND code_c='{$code_c}' ";
				if($code_d!="000") {
					$sql.= "AND code_d='{$code_d}' ";
				}
			}
		}
	} else {
		$sql.= "AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	}
	pmysql_query($sql,get_db_conn());
	$detail_type=$design;

	$onload="<script>parent.ModifyCodeDesign('{$code}','{$design}','{$is_design}');alert(\"상품 상세화면 템플릿 변경이 완료되었습니다.\");</script>";
}
include("header.php"); 
?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	ischk=false;
	if(typeof(document.form1.design.length)!="undefined") {
		for(i=0;i<document.form1.design.length;i++) {
			if(document.form1.design[i].checked) {
				ischk=true;
				break;
			}
		}
	} else {
		if(document.form1.design.checked) {
			ischk=true;
		}
	}
	if(!ischk) {
		alert("디자인 템플릿을 선택하세요.");
		return;
	}
	if(confirm("상품 상세화면 템플릿을 변경하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}

function SkinList() {
	document.form1.mode.value="";
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function ChangeDesign(tmp) {
	if(typeof(document.form1["design"][tmp])=="object") {
		document.form1["design"][tmp].checked=true;
		parent.design_preview(document.form1["design"][tmp].value);
	} else {
		document.form1["design"].checked=true;
		parent.design_preview(document.form1["design"].value);
	}
}

function changeMouseOver(img) {
	 img.style.border='1 dotted #999999';
}
function changeMouseOut(img,dot) {
	 img.style.border="1 "+dot;
}

//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css">
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=type value="<?=$type?>">
<input type=hidden name=detail_type value="<?=$detail_type?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<tr>
	<td>
	<TABLE WIDTH=445 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG <?php if($sch=="AD")echo"SRC=\"images/img_tapstart.gif\""; else echo"SRC=\"images/img_tapstartr.gif\"";?> ALT=""></TD>
		<TD width="202" <?php if($sch=="AD")echo"background=\"images/img_tapbg.gif\" class=\"font_white\""; else echo"background=\"images/img_tapbgr.gif\"";?>><input type=radio id="idx_sch0" name="sch" value="AD" <?php if($sch=="AD")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="SkinList()"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sch0>일반 쇼핑몰 디자인 타입</label></TD>
		<TD><IMG <?php if($sch=="AD")echo"SRC=\"images/img_tap_end.gif\""; else echo"SRC=\"images/img_tap_endr.gif\"";?> ALT=""></TD>
		<TD><IMG <?php if($sch=="BD")echo"SRC=\"images/img_tapstart.gif\""; else echo"SRC=\"images/img_tapstartr.gif\"";?> ALT=""></TD>
		<TD width="202" <?php if($sch=="BD")echo"background=\"images/img_tapbg.gif\" class=\"font_white\""; else echo"background=\"images/img_tapbgr.gif\"";?>><input type=radio id="idx_sch1" name="sch" value="BD" <?php if($sch=="BD")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="SkinList()"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sch1>가격 고정형 디자인 타입</label></TD>
		<TD><IMG <?php if($sch=="BD")echo"SRC=\"images/img_tap_end.gif\""; else echo"SRC=\"images/img_tap_endr.gif\"";?> ALT=""></TD>
	</TR>
	</TABLE>
	</TD>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" bgcolor="#0099CC">
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD width="100%"><div class="point_title">템플릿 선택하기</div></TD>
			</TR>
			<TR>
				<TD width="100%" style="padding:10pt;" bgcolor="#f8f8f8">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
<?php
	$sql = "SELECT code FROM tblproductdesigntype 
	WHERE code LIKE '{$sch}%' ORDER BY code ASC"; 
	$result = pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		if($i==0) echo "<tr>\n";
		if($i>0 && $i%3==0) echo "</tr>\n<tr>\n";
		if($i%3==0) {
			echo "<td><p align=\"center\">";
		} else {
			echo "<td><p align=\"center\">";
		}
		echo "<img src=\"images/product/{$row->code}.gif\" border=\"0\" class=\"imgline1\" onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign({$i});'>";
		echo "<br><input type=radio id=\"idx_design{$i}\" name=design value=\"{$row->code}\" ";
		if($detail_type==$row->code) echo "checked";
		echo " onclick=\"parent.design_preview('{$row->code}')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\">";
		echo "</td>\n";
		$i++;
	}
	pmysql_free_result($result);
	if($i%3!=0) {
		for($j=(3-($i%3));$j<=3;$j++)	echo "<td align=center>&nbsp;</td>\n";
	}
	if($i>0) {
		echo "</tr>\n";
	}
?>
					</table>
					</td>
				</tr>
				</table>
				</TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<tr>
	<td height=10></td>
</tr>
<?php if(ord($type) && !strstr($type,"X")){?>
<tr>
	<td align=center><input type=checkbox id="idx_design0" name=is_design value="1"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_design0><span class=font_orange><B>선택된 템플릿으로 하위 카테고리 적용</B></span></label></td>
</tr>
<?php }?>
<tr>
	<td height=20></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
</tr>
</form>
</table>
<?=$onload?>
</body>
</html>
